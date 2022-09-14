<?php

namespace App\Http\Controllers;

use App\Classes\SystemID;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Jenssegers\Agent\Agent;
use Log;
use Yajra\DataTables\DataTables;

class CStoreAuditedRptController extends Controller
{
    //
    public function getAuditedNotes()
    {
        return view('cstore_audited_rpt.cstore_audited_report_list');
    }

    public function getAuditedList()
    {
        $location = DB::table('location')->first();
        $user = auth()->user();
        $time = date('dMy H:i:s');
        $agent = new Agent();

        $inventory_products = DB::table('prd_inventory')->
            leftjoin('product', 'product.id', '=', 'prd_inventory.product_id')->
            leftjoin('localprice', 'localprice.product_id', '=', 'prd_inventory.product_id')->
            leftjoin('locationproduct', 'locationproduct.product_id', '=', 'localprice.product_id')->
            select(
            'product.*',
            'locationproduct.quantity as qty',
        )->
            latest()->get();

        Log::info('inventory_products:' . json_encode($inventory_products));

        $openitem_products = DB::table('prd_openitem')->
            leftjoin('product', 'product.id', '=', 'prd_openitem.product_id')->
            select(
            'product.*',
            'prd_openitem.qty as qty',
        )->latest()->get();

        $audited_report_list = $inventory_products->merge($openitem_products);

        return view(
            'cstore_audited_rpt.cstore_audited_report',
            [
                'time' => $time,
                'location' => $location,
                'user' => $user,
                'audited_report_list' => $audited_report_list,
                'agent' => $agent,
                'datalength' => count($inventory_products) + count($openitem_products),

            ]
        );
    }

    public function updateAuditReport(Request $request)
    {
        $allproduct = $this->getProducts();
        $container = json_decode(json_encode($request->container, true));
        $container = collect($container);

        $allproduct->each(function ($item, $key) use ($container) {

            if ($container->contains('id', $item->id)) {

                $item->audited_qty = $container->where('id', $item->id)->first()->audited_qty;
                $item->diff = $container->where('id', $item->id)->first()->diff;
                $item->qty = $container->where('id', $item->id)->first()->qty;
            }
        });

        $s = new SystemID('auditedreport');
        foreach ($allproduct as $key => $value) {

            $data = array(
                'product_id' => number_format($value->id),
                'qty' => number_format(($value->qty < 0) ? 0 : $value->qty),
                'audited_qty' => number_format($value->audited_qty),
                'difference' => number_format($value->diff),
                'systemid' => $s->__toString(),
                'created_at' => date('Y-m-d H:i:s', time()),
            );
            DB::table('auditedreport')->insert($data);
        }

        $data2 = array(
            'auditedreport_systemid' => $s->__toString(),
            'created_at' => date('Y-m-d H:i:s', time()),
        );

        DB::table('auditedreport_list')->insert($data2);
    }

    public function view_audited_report($audited_note_list_id)
    {
        $main = DB::table('auditedreport_list')->
            where('auditedreport_systemid', $audited_note_list_id)->
            first();

        $list = DB::table('auditedreport')->
            where('auditedreport.systemid', $main->auditedreport_systemid)->
            leftjoin('product', 'product.id', '=', 'auditedreport.product_id')->
            select(
            'auditedreport.*',
            'product.id as p_id',
            'product.name as name',
            'product.thumbnail_1 as thumbnail_1',
            'product.systemid as psystemid')->
            get();

        $result = [];
        foreach ($list as $lt) {

            $barcode = DB::table('productbarcode')
                ->where('product_id', $lt->p_id)
                ->where('selected', 1)
                ->first();

            if (is_null($barcode)) {
                $lt->barcode = $lt->psystemid;
            } else {
                $lt->barcode = $barcode->barcode;
            }

            array_push($result, $lt);
        }

        $list = $result;

        $location = DB::table('location')->first();
        $user = auth()->user();
        $time = date('dMy H:i:s', strtotime($main->created_at));
        $docId = $audited_note_list_id;

        return view('cstore_audited_rpt.cstore_audited_report_confirmed',
            compact('list', 'location', 'user', 'docId', 'time'));
    }

    public function mobileView()
    {
        $location = DB::table('location')->first();
        $user = auth()->user();
        $time = date('dMy H:i:s');

        $inventory_products = DB::table('prd_inventory')
            ->leftjoin('product', 'product.id', '=', 'prd_inventory.product_id')
            ->leftjoin('localprice', 'localprice.product_id', '=', 'prd_inventory.product_id')
            ->leftjoin('locationproduct', 'locationproduct.product_id', '=', 'localprice.product_id')
            ->select(
                'product.*',
                'locationproduct.quantity as qty',
            )
            ->latest()->get();
        $openitem_products = DB::table('prd_openitem')
            ->leftjoin('product', 'product.id', '=', 'prd_openitem.product_id')
            ->select(
                'product.*',
                'prd_openitem.qty as qty',
            )->latest()->get();
        $audited_report_list = $inventory_products->merge($openitem_products);

        return view('cstore_audited_rpt.mob_audited_report', ['time' => $time, 'location' => $location, 'user' => $user, 'audited_report_list' => $audited_report_list]);
    }

    public function listPrdAuditedRpt()
    {
        try {

            $data = DB::table('auditedreport_list')->latest()->get();
            return Datatables::of($data)->addIndexColumn()->addColumn('created_at', function ($data) {
                return date('dMy H:i:s', strtotime($data->created_at));
            })->make(true);

        } catch (Exception $e) {
            return ["message" => $e->getMessage(), "error" => false];
        }
    }

    public function listAuditedRpt(Request $request)
    {
        $products = $this->getProducts();

        foreach ($products as $k) {
            # code...

            if ($request->has('container')) {

                foreach ($request->container as $i) {

                    if ($k->id == $i['id']) {
                        $k->audited_qty = $i['audited_qty'];
                        $k->diff = $i['diff'];
                    }
                }
            }
        }
        $products = $this->get_product_barcode($products);
        return $this->updated_display($products);

    }

    public function get_product_barcode($products)
    {
        $systemids = [];
        foreach ($products as $prd) {
            array_push($systemids, $prd->systemid);
        }

        $product_ids = DB::table('product')
            ->whereIn('systemid', $systemids)
            ->get();

        $prod = [];
        for ($i = 0; $i < sizeof($products); $i++) {
            $prd = $products[$i];
            $p = $product_ids[$i];

            $barcode = DB::table('productbarcode')
                ->where('product_id', $p->id)
                ->where('selected', 1)
                ->first();

            if (is_null($barcode)) {
                $prd->barcode = $prd->systemid;
            } else {
                $prd->barcode = $barcode->barcode;
            }

            array_push($prod, $prd);
        }

        return $prod;
    }

    public function getProducts()
    {

        $location = DB::table('location')->first();
        $stck = DB::select(DB::raw("
            SELECT
                pi.product_id,
                p.name,
                p.systemid,
                sum(srp.quantity) as quantity
            FROM
                stockreportproduct srp,
                prd_inventory pi,
                product p
            WHERE
                p.id = pi.product_id and
                pi.product_id = srp.product_id
            GROUP BY
                pi.product_id,
                p.name,
                p.systemid;
            ")
        );

        foreach ($stck as $s) {

            DB::table('locationproduct')
                ->updateOrInsert(
                    ['product_id' => $s->product_id],
                    ['quantity' => $s->quantity,
                        'location_id' => $location->id,
                        'damaged_quantity' => 0],
                );
        }

        $inventory_products = DB::table('prd_inventory')->
            leftjoin('product', 'product.id', '=', 'prd_inventory.product_id')->
            leftjoin('localprice', 'localprice.product_id', '=', 'prd_inventory.product_id')->
            leftjoin('locationproduct', 'locationproduct.product_id', '=', 'localprice.product_id')->
            select(
            'product.id as id',
            'product.name as name',
            'product.thumbnail_1 as thumbnail_1',
            'product.systemid as systemid',
            'locationproduct.quantity  as qty',
            'locationproduct.quantity  as audited_qty',
            DB::raw('null as diff'),
            'product.ptype as type',
        )->groupBy('id', 'systemid', 'name', 'thumbnail_1', 'locationproduct.quantity', 'ptype')->get();

        $openitem_products = DB::table('prd_openitem')->
            leftjoin('product', 'product.id', '=', 'prd_openitem.product_id')->
            select(
            'product.id as id',
            'product.name as name',
            'product.thumbnail_1 as thumbnail_1',
            'product.systemid as systemid',
            'prd_openitem.qty as qty',
            'prd_openitem.qty as audited_qty',
            DB::raw('null as diff'),
            'product.ptype as type',
        )->get();

        $data = $inventory_products->merge($openitem_products);
        return $data;
    }

    public function displayTable($data)
    {
        try {
            return Datatables::of($data)->addIndexColumn()->
                addColumn('product_systemid', function ($data) {
                return $data->systemid;
            })->
                addColumn('product_name', function ($data) {
                $img_src = '/images/product/' .
                $data->systemid . '/thumb/' .
                $data->thumbnail_1;

                $pub_path = public_path($img_src);

                if (!empty($data->thumbnail_1) && file_exists($pub_path)) {
                    $img = "<img src=" . asset($img_src) .
                        " data-field='inven_pro_name' style=' width: 25px;
                        height: 25px;display: inline-block;margin-right: 8px;object-fit:contain;'>";
                } else {
                    $img_src = '';
                    $img = '';
                }

                return $img . $data->name;
            })->
                addColumn('product_qty', function ($data) {
                $qty = app("App\Http\Controllers\CentralStockMgmtController")->
                    qtyAvailable($data->id);

                Log::info('$data->id=' . $data->id);
                Log::info('$qty=' . $qty);

                $qty = !empty($qty) ? $qty : 0;
                return <<<EOD
						<span id="qty_$data->id"  data-field="$data->type">$qty</span>
EOD;
            })->
                addColumn('audited_qty', function ($data) {
                $product_id = $data->id;
                // return view('fuel_stockmgmt.inven_qty', compact('product_id'));
                $val = $data->id;
                $qty = app("App\Http\Controllers\CentralStockMgmtController")->
                    qtyAvailable($data->id);
                $qty = !empty($qty) ? $qty : 0;

                $incr = '<div class="align-self-center value-button increase" id="increase_' .
                    $val . '" onclick="increaseValue(\'' . $val . '\')" value="Increase Value">
                            <ion-icon class="ion-ios-plus-outline" style="cursor: pointer;font-size: 24px;margin-right:5px;">
                            </ion-icon>
                        </div>';
                //
                $input = '<input type="number" id="number_' . $val .
                    '" oninput="changeValueOnBlur(\'' . $val . '\')" class="number product_qty js-product-qty"
                    value="' . $qty . '" min="0"/>';

                $decr = '<div class="value-button decrease" id="decrease_' . $val .
                    '" onclick="decreaseValue(\'' . $val . '\')" value="Decrease Value">
                            <ion-icon class="ion-ios-minus-outline" style="cursor: pointer;font-size: 24px;margin-left:5px;">
                            </ion-icon>
                        </div>';

                $full_div = '<div class="d-flex align-items-center justify-content-center">' .
                    $incr . $input . $decr . '</div>';
                return $full_div;
            })->
                addColumn('difference', function ($data) {
                $product_id = $data->id;
                $qty = app("App\Http\Controllers\CentralStockMgmtController")->
                    qtyAvailable($data->id);
                $diff = $qty - $qty;
                return <<<EOD
                                 <div id="diff_$product_id">$diff </div>
EOD;
            })->
                rawColumns(['difference'])->escapeColumns([])->make(true);
        } catch (Exception $e) {
            return ["message" => $e->getMessage(), "error" => false];
        }
    }

    public function updated_display($data)
    {
        try {
            return Datatables::of($data)->
                addIndexColumn()->
                addColumn('product_systemid', function ($data) {
                return $data->systemid;
            })->addColumn('barcode', function ($data) {
                return $data->barcode;

            })->addColumn('product_name', function ($data) {
                $img_src = '/images/product/' .
                $data->systemid . '/thumb/' .
                $data->thumbnail_1;

                $pub_path = public_path($img_src);

                if (!empty($data->thumbnail_1) && file_exists($pub_path)) {
                    $img = "<img src=" . asset($img_src) .
                        " data-field='inven_pro_name' style=' width: 25px;
                        height: 25px;display: inline-block;margin-right: 8px;object-fit:contain;'>";
                } else {
                    $img_src = '';
                    $img = '';
                }

                return $img . $data->name;
            })->addColumn('product_qty', function ($data) {
                $qty = !empty($data->qty) ? $data->qty : 0;

                return <<<EOD
						<span id="qty_$data->id"  data-field="$data->type">$qty</span>
EOD;
            })->addColumn('audited_qty', function ($data) {
                $product_id = $data->id;
                // return view('fuel_stockmgmt.inven_qty', compact('product_id'));
                $val = $data->id;

                $qty = !empty($data->audited_qty) ? $data->audited_qty : 0;

                $incr = '<div class="align-self-center value-button increase" id="increase_' .
                    $val . '" onclick="increaseValue(\'' . $val . '\')" value="Increase Value">
                            <ion-icon class="ion-ios-plus-outline" style="cursor: pointer;font-size: 24px;margin-right:5px;">
                            </ion-icon>
                        </div>';
                //
                $input = '<input type="number" id="number_' . $val .
                    '" oninput="changeValueOnBlur(\'' . $val . '\')" class="number product_qty js-product-qty"
                    value="' . $qty . '" min="0"/>';

                $decr = '<div class="value-button decrease" id="decrease_' . $val .
                    '" onclick="decreaseValue(\'' . $val . '\')" value="Decrease Value">
                            <ion-icon class="ion-ios-minus-outline" style="cursor: pointer;font-size: 24px;margin-left:5px;">
                            </ion-icon>
                        </div>';

                $full_div = '<div class="d-flex align-items-center justify-content-center">' .
                    $incr . $input . $decr . '</div>';
                return $full_div;
            })->addColumn('difference', function ($data) {
                $product_id = $data->id;

                $diff = $data->audited_qty - $data->qty;
                return <<<EOD
                                 <div id="diff_$product_id">$diff </div>
EOD;
            })->rawColumns(['difference'])->escapeColumns([])->make(true);
        } catch (Exception $e) {
            return ["message" => $e->getMessage(), "error" => false];
        }
    }
}
