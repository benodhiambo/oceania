<?php

namespace App\Http\Controllers;

use App\Classes\SystemID;
use App\Models\Company;
use App\Models\Location;
use App\Models\Product;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\DataTables;

class ReceivingNoteController extends Controller
{
    public function getReceivingNotes()
    {
        return view('receiving_note.cstore_receiving_note_list');
    }

    public function displayReceievingNoteList()
    {
        $data = DB::select(DB::raw('
            SELECT
                rl.recvnote_systemid,
                r.invoice_no,
                rl.created_at
            FROM
                recvnote r,
                recvnotelist as rl
            WHERE
                r.systemid = rl.recvnote_systemid
            GROUP BY
                r.invoice_no,
                rl.recvnote_systemid,
                rl.created_at
            ORDER BY
                rl.created_at desc;
            ')
        );

        try {

            return Datatables::of($data)->addIndexColumn()->
                addColumn('document_no', function ($data) {

                $link = '<a href="/receiving_list_id/' . $data->
                    recvnote_systemid . '" style="text-decoration:none;" target="_blank">' .
                $data->recvnote_systemid . '</a>';
                return $link;
            })->

                addColumn('invoice_no', function ($data) {
                return $data->invoice_no;
            })->

                addColumn('created_at', function ($data) {
                return date('dMy H:i:s', strtotime($data->created_at));
            })->escapeColumns([])->make(true);

        } catch (Exception $e) {
            return [
                "message" => $e->getMessage(),
                "error" => true,
            ];
        }
    }

    public function getReceiveingList()
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
                'locationproduct.product_id as product_id',
                'locationproduct.quantity as Iqty',
                'locationproduct.costvalue as Icostvalue',
                'localprice.recommended_price as Iprice',
            )
            ->latest()->get();

        $inventory_prod = $this->get_latest_cost($inventory_products, 'locprod_productledger', 'locprod');

        $openitem_products = DB::table('prd_openitem')
            ->leftjoin('product', 'product.id', '=', 'prd_openitem.product_id')
            ->select(
                'product.*',
                'prd_openitem.product_id as product_id',
                'prd_openitem.costvalue as Icostvalue',
                'prd_openitem.price as Iprice',
                'prd_openitem.qty as Iqty',
            )->latest()->get();

        $openitem_prod = $this->get_latest_cost($openitem_products, 'openitem_productledger', 'openitem');

        $report_list = $inventory_prod->merge($openitem_prod);

        $report_list = $this->get_product_barcode($report_list);

        return view('receiving_note.cstore_receiving_note',
            ['time' => $time, 'location' => $location,
                'user' => $user, 'receivingnotes' => $report_list]);
    }

    public function get_latest_cost($data, $tbl, $type)
    {

        $updated_data = collect();

        foreach ($data as $prd) {
            $prd_info = DB::table('product')->
                whereId($prd->product_id)->
                whereNull('deleted_at')->
                first();

            $prd->cost = 0;

            if (!empty($prd_info)) {
                $tbl = ($prd_info->ptype == 'openitem') ? 'openitem_productledger' : 'locprod_productledger';

                $latest_cost = DB::table($tbl)->
                    where('product_systemid', $prd_info->systemid)->
                    whereNotNull('cost')->
                    orderBy('created_at', 'desc')->
                    first();

                $prd->Icost = empty($latest_cost) ? 0 : $latest_cost->cost;
                $prd->Icostvalue = $prd->Icost * $prd->Iqty;
                $prd->cost_id = empty($latest_cost) ? 0 : $latest_cost->id;
            }
            $updated_data->push($prd);
        }

        return $updated_data;
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

    public function get_receiving_note_datatable(Request $request)
    {

        $prds = $this->get_product();

        foreach ($prds as $i) {
            if (($request->has('stockin_updates'))) {

                foreach ($request->stockin_updates as $k) {
                    # code...

                    if ($i->id == $k['product_id']) {
                        $i->Icost = $k['cost'] * 100;
                        $i->Iqty = $k['qty'];
                        $i->Icostvalue = $k['cv'] * 100;
                    }

                }

            }

        }
        $prds = $this->get_product_barcode($prds);
        return $this->display_table($prds);
    }

    public function get_confirmed_note_datatable(Request $request)
    {
        $main = DB::table('recvnotelist')->
            where('recvnote_systemid', $request->systemid)->first();

        $location = DB::table('location')->first();
        $user = auth()->user();
        $time = date('dMy H:i:s');

        $list = DB::table('recvnote')
            ->where('recvnote.systemid', '=', $request->systemid)
            ->leftjoin('product', 'product.systemid', '=', 'recvnote.product_id')
            ->select('recvnote.*', 'product.name as name', 'product.thumbnail_1 as thumbnail_1')
            ->get();

        $result = [];
        foreach ($list as $lt) {
            $barcode = DB::table('productbarcode')
                ->where('selected', 1)
                ->where('product_id', $lt->product_id)
                ->first();
            if (is_null($barcode)) {
                $lt->barcode = $lt->systemid;
            } else {
                $lt->barcode = $barcode->barcode;
            }
            array_push($result, $lt);
        }

        $list = $result;

        $location = DB::table('location')->first();
        $user = auth()->user();
        $time = date('dMy H:i:s', strtotime($main->created_at));
        $docId = $request->systemid;

        try {
            return Datatables::of($list)->
                addIndexColumn()->
                addColumn('systemid', function ($data) {
                $product = DB::table('product')->
                    whereId($data->product_id)->first();

                $val = 1;
                $span = '<span id="product_' . $val . '" >' . $product->systemid . ' </span>';
                return $span;
            })->
                addColumn('product_name', function ($data) {
                $product = DB::table('product')->
                    whereId($data->product_id)->first();

                $img = '';

                $img_url = '/images/product/' . $product->systemid . '/thumb/' . $product->thumbnail_1;
                if (!empty($product->thumbnail_1) && file_exists(public_path() . $img_url)) {
                    $img = '<img src="' . $img_url . '" alt="imf" style="height:25px;width:25px;">';
                }
                return $img . ' ' . $product->name;
            })->
                addColumn('product_price', function ($data) {
                $val = 1;
                $span = '<span id="price_' . $val . '" >' .
                number_format($data->price / 100, 2) . ' </span>';

                return $span;
            })->
                addColumn('qty', function ($data) {
                $val = 1;
                $span = '<span id="product_' . $val . '">' . $data->qty . ' </span>';
                return $span;
            })->
                addColumn('barcode', function ($data) {
                return $data->barcode;
            })->
                addColumn('cost', function ($data) {
                $val = 1;
                $cost = '<a id="cost_' . $val . '">' .
                number_format($data->cost / 100, 2) . ' </a>';

                return $cost;
            })->
                addColumn('costvalue', function ($data) {
                $val = 1;
                $span = '<span id="costvalue_' . $val . '" >' .
                number_format(($data->qty * $data->cost) / 100, 2) . ' </span>';

                return $span;
            })->
                escapeColumns([])->
                make(true);

        } catch (Exception $e) {
            return [
                "message" => $e->getMessage(),
                "error" => true,
            ];
        }
    }

    public function update_receive_notes(Request $request)
    {
        $s = new SystemID('recvnote');

        foreach ($request->container as $product) {

            $data = array(
                'product_id' => $product['product_id'],
                'qty' => str_replace(',', '', $product['qty']),
                'cost' => str_replace(',', '', $product['cost']) * 100,
                'price' => str_replace(',', '', $product['price']) * 100,
                'costvalue' => str_replace(',', '', $product['costvalue']) * 100,
                'systemid' => $s->__toString(),
                'invoice_no' => $product['invoice_no'],
                'created_at' => date('Y-m-d H:i:s', time()),
                'updated_at' => date('Y-m-d H:i:s', time()),
            );
            DB::table('recvnote')->insert($data);
        }

        $data2 = array('recvnote_systemid' => $s->__toString(),
            'created_at' => date('Y-m-d H:i:s', time()));

        DB::table('recvnotelist')->insert($data2);

        $this->do_stockin($request->container, $s->__toString());
    }

    public function do_stockin($data, $rec_systemid)
    {

        $user_id = \Auth::user()->id;
        $stock_system = $rec_systemid;

        $company = Company::first();
        $location = Location::first();
        $type = 'received';

        foreach ($data as $key => $value) {
            Log::debug('***do_stockin()*** $value=' . json_encode($value));

            //Stock Report
            $stockreport_id = DB::table('stockreport')->insertGetId([
                'systemid' => $stock_system,
                'creator_user_id' => $user_id,
                'type' => 'received',
                'location_id' => $location->id,
                "created_at" => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            DB::table('stockreportproduct')->insert([
                "stockreport_id" => $stockreport_id,
                "product_id" => $value['product_id'],
                "quantity" => str_replace(',', '', $value['qty']),
                "created_at" => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            $prd = DB::table('product')->
                whereId($value['product_id'])->
                first();

            if ($value['ptype'] == 'openitem') {

                $latest_cost = DB::table('openitem_productledger')->
                    where('product_systemid', $prd->systemid)->
                    whereNotNull('cost')->
                    orderBy('created_at', 'desc')->
                    first();

                $cost = empty($latest_cost) ? 0 : $latest_cost->cost;

                $openitemprodid = DB::table('openitem_productledger')->
                    insertGetId([
                    "stockreport_id" => $stockreport_id,
                    "product_systemid" => $prd->systemid,
                    "qty" => str_replace(',', '', $value['qty']),
                    "cost" => str_replace(',', '', $value['cost']) * 100,
                    "last_update" => date('Y-m-d H:i:s'),
                    "status" => 'active',
                    "type" => $type,
                    "deleted_at" => null,
                    "created_at" => date('Y-m-d H:i:s'),
                    "updated_at" => date('Y-m-d H:i:s'),
                ]);

                $costid = DB::table('openitem_cost')->
                    insertGetId([
                    "openitemprodledger_id" => $openitemprodid,
                    "qty_in" => str_replace(',', '', $value['qty']),
                    "qty_out" => 0,
                    "balance" => str_replace(',', '', $value['qty']),
                    "cost" => str_replace(',', '', $value['cost']) * 100,
                    "deleted_at" => null,
                    "created_at" => date('Y-m-d H:i:s'),
                    "updated_at" => date('Y-m-d H:i:s'),
                ]);

            } else {

                $latest_cost = DB::table('locprod_productledger')->
                    where('product_systemid', $prd->systemid)->
                    whereNotNull('cost')->
                    orderBy('created_at', 'desc')->
                    first();

                $cost = empty($latest_cost) ? 0 : $latest_cost->cost;

                $locprodid = DB::table('locprod_productledger')->
                    insertGetId([
                    "stockreport_id" => $stockreport_id,
                    "product_systemid" => $prd->systemid,
                    "qty" => str_replace(',', '', $value['qty']),
                    "cost" => str_replace(',', '', $value['cost']) * 100,
                    "last_update" => date('Y-m-d H:i:s'),
                    "status" => 'active',
                    "type" => $type,
                    "deleted_at" => null,
                    "created_at" => date('Y-m-d H:i:s'),
                    "updated_at" => date('Y-m-d H:i:s'),
                ]);

                $costid = DB::table('locationproduct_cost')->
                    insertGetId([
                    "locprodprodledger_id" => $locprodid,
                    "qty_in" => str_replace(',', '', $value['qty']),
                    "qty_out" => 0,
                    "balance" => str_replace(',', '', $value['qty']),
                    "cost" => str_replace(',', '', $value['cost']) * 100,
                    "deleted_at" => null,
                    "created_at" => date('Y-m-d H:i:s'),
                    "updated_at" => date('Y-m-d H:i:s'),
                ]);
            }
        }
    }

    public function get_datatable_products()
    {
        try {
            $open = DB::table('prd_openitem')->join('product', 'prd_openitem.product_id', '=', 'product.id')->select(
                'product.systemid',
                'prd_openitem.product_id',
                'prd_openitem.price',
                'prd_openitem.cost',
                'prd_openitem.qty',
                'prd_openitem.deleted_at',
                'prd_openitem.created_at',
                'prd_openitem.updated_at'
            )->get();

            $inv = DB::table('prd_inventory')->join('product', 'prd_inventory.product_id', '=', 'product.id')->select(
                'product.systemid',
                'prd_inventory.product_id',
                'prd_inventory.price',
                'prd_inventory.cost',
                'prd_inventory.qty',
                'prd_inventory.deleted_at',
                'prd_inventory.created_at',
                'prd_inventory.updated_at'
            )->get();

            $merged = $open->merge($inv);

            foreach ($merged as $prd) {
                $prd->product = DB::table('product')->whereId($prd->product_id)->first();

                $prd->img_src = '/images/product/' .
                $prd->systemid . '/thumb/' .
                $prd->product->thumbnail_1;
            }

            return Datatables::of($merged)->addIndexColumn()->editColumn('cost', function ($data) {
                $data = new \Illuminate\Support\Collection($data);
                $cost = $data;
                return $cost;
            })->editColumn('qty', function ($data) {
                $data = new \Illuminate\Support\Collection($data);
                Log::debug([
                    '$data-in-qty' => $data,
                ]);
                $qty = $data;
                return $qty;
            })->editColumn('cost_value', function ($data) {
                $data = new \Illuminate\Support\Collection($data);
                $cost_value = $data;
                return $cost_value;
            })->editColumn('loyalty', function ($data) {
                $data = new \Illuminate\Support\Collection($data);
                $loyalty = $data;
                return $loyalty;
            })->editColumn('royalty', function ($data) {
                $data = new \Illuminate\Support\Collection($data);
                $royalty = $data;
                return $royalty;
            })->editColumn('price', function ($data) {
                $data = new \Illuminate\Support\Collection($data);
                $price = $data;
                return $price;
            })->addColumn('action', function ($data) {
                $data = new \Illuminate\Support\Collection($data);
                $product_id = $data;
                return view('fuel_stockmgmt.inven_qty', compact('product_id'));
            })->rawColumns(['action'])->make(true);
        } catch (Exception $e) {
            return ["message" => $e->getMessage(), "error" => false];
        }
    }

    public function getReceivingNote($id)
    {
        $main = DB::table('recvnotelist')->where('recvnote_systemid', $id)->first();
        $list = DB::table('recvnote')
            ->where('recvnote.systemid', '=', $id)
            ->leftjoin('product', 'product.systemid', '=', 'recvnote.product_id')
            ->select('recvnote.*', 'product.name as name', 'product.thumbnail_1 as thumbnail_1')
            ->get();

        $location = DB::table('location')->first();
        $user = auth()->user();
        $time = date('dMy H:i:s', strtotime($main->created_at));
        $docId = $id;

        $invoice_data = DB::table('recvnote')->where('systemid', $id)->first();
        $invoice_no = empty($invoice_data) ? 0 : $invoice_data->invoice_no;

        return view('receiving_note.cstore_receiving_note_confirmed', compact('list', 'location', 'user', 'invoice_no', 'docId', 'time'));
    }

    public function search_barcode(Request $request)
    {

        try {
            $search_string = $request->barcode;

            $is_matrix = false;
            $barcode = DB::table('productbarcode')->
                where('barcode', $search_string)->
                whereNull('deleted_at')->first();

            if (empty($barcode)) {
                $barcode = DB::table('productbmatrixbarcode')->
                    where('bmatrixbarcode', $search_string)->
                    whereNull('deleted_at')->
                    first();
                $is_matrix = true;
            }

            $product_id = $barcode->product_id;

            // If product is from prd_inventory
            $product = DB::table('product')->
                select('product.*', 'locationproduct.quantity as qty')->
                join('locationproduct', 'locationproduct.product_id', 'product.id')->
                where('product.id', $product_id)->
                whereNull('product.deleted_at')->
                first();

            // If product is from prd_openitem
            if (empty($product)) {
                $product = DB::table('product')->
                    select('product.*', 'prd_openitem.qty as qty')->
                    join('prd_openitem', 'prd_openitem.product_id', 'product.id')->
                    where('product.id', $product_id)->
                    whereNull('product.deleted_at')->
                    first();
            }

            $prod_sorted = $this->get_product()->sortByDesc('Iqty');

            foreach ($prod_sorted as $value) {
                # code...
                if ($value->id == $product->id) {

                    $value->Iqty = (int) $request->current_qty + 1;
                }
            }
            if (!empty($request->changed_product)) {
                foreach ($prod_sorted as $p) {
                    # code...
                    foreach ($request->changed_products as $c) {
                        # code...
                        if ($p->product_id == $c['product_id']) {
                            $p->Icost = $c['new_cost'];
                        }
                    }
                }
            }

            return $this->display_table($prod_sorted);
        } catch (Exception $e) {
            Log::error([
                "Error: " => $e->getMessage(),
                "Line: " => $e->getLine(),
                "File: " => $e->getFile(),
            ]);

            return response()->json([
                "message" => "Barcode not found",
                "error" => true,
            ]);

            //abort(404);
        }
    }

    public function get_product()
    {
        $inventory_products = DB::table('prd_inventory')
            ->leftjoin('product', 'product.id', '=', 'prd_inventory.product_id')
            ->leftjoin('localprice', 'localprice.product_id', '=', 'prd_inventory.product_id')
            ->leftjoin('locationproduct', 'locationproduct.product_id', '=', 'localprice.product_id')
            ->select(
                'product.*',
                'locationproduct.quantity as Iqty',
                'prd_inventory.product_id as product_id',
                'locationproduct.cost as Icost',
                'locationproduct.costvalue as Icostvalue',
                'localprice.recommended_price as Iprice',
            )
            ->latest()->get();

        $inventory_prds = $this->get_latest_cost($inventory_products, 'locprod_productledger', 'locprod');

        $openitem_products = DB::table('prd_openitem')
            ->leftjoin('product', 'product.id', '=', 'prd_openitem.product_id')
            ->select(
                'product.*',
                'prd_openitem.product_id as product_id',
                'prd_openitem.costvalue as Icostvalue',
                'prd_openitem.price as Iprice',
                'prd_openitem.cost as Icost',
                'prd_openitem.qty as Iqty',
            )->latest()->get();

        $openitem_prds = $this->get_latest_cost($openitem_products, 'openitem_productledger', 'openitem');

        $report_list = $inventory_prds->merge($openitem_prds);

        $updated_data = collect();

        foreach ($report_list as $k => $prd) {
            $prd->num = $k;
            $prd->Icostvalue = 0;
            $updated_data->push($prd);
        }

        return $updated_data;
    }

    public function display_table($updated_data)
    {
        // dd($updated_data);
        try {
            return Datatables::of($updated_data)->
                addIndexColumn()->
                addColumn('systemid', function ($data) {

                $val = (int) $data->num + 1;
                $span = '<span id="product_' . $val . '" data-prd_id="' .
                $data->product_id . '" data-ptype="' . $data->ptype . '" >' .
                $data->systemid . ' </span>';

                return $span;

            })->addColumn('barcode', function ($data) {
                return $data->barcode;

            })->addColumn('product_name', function ($data) {
                $img = '';
                return $data->name;
            })->addColumn('product_name', function ($data) {
                $img = '';

                $img_url = '/images/product/' . $data->systemid . '/thumb/' . $data->thumbnail_1;
                if (!empty($data->thumbnail_1) && file_exists(public_path() . $img_url)) {
                    $img = '<img src="/images/product/' .
                    $data->systemid . '/thumb/' . $data->thumbnail_1 .
                        '" alt="imf" style="height:25px;width:25px;">';
                }
                return $img . ' ' . $data->name;
            })->addColumn('product_price', function ($data) {
                $val = (int) $data->num + 1;
                $span = '<span id="price_' . $val . '" >' .
                number_format($data->Iprice / 100, 2) . ' </span>';

                return $span;
            })->addColumn('qty', function ($data) {
                $val = (int) $data->num + 1;

                // $qty = !is_null($data->Iqty)? $data->Iqty:0;
                // dd($qty);
                $incr = '<div class="align-self-center value-button increase" id="increase_' .
                    $val . '" onclick="increaseValue(\'' . $val . '\')" value="Increase Value">
					<ion-icon class="ion-ios-plus-outline" style="cursor: pointer;font-size: 24px;margin-right:5px;">
					</ion-icon>
				</div>';
                //
                $input = '<input type="number" id="number_' . $val .
                    '" oninput="changeValueOnBlur(\'' . $val .
                    '\')" class="number product_qty js-product-qty" value="0" min="0"/>';

                $decr = '<div class="value-button decrease" id="decrease_' . $val .
                    '" onclick="decreaseValue(\'' . $val . '\')" value="Decrease Value">
					<ion-icon class="ion-ios-minus-outline" style="cursor: pointer;font-size: 24px;margin-left:5px;">
					</ion-icon>
				</div>';

                $full_div = '<div class="d-flex align-items-center justify-content-center">' .
                    $incr . $input . $decr . '</div>';
                return $full_div;
            })->addColumn('cost', function ($data) {
                $val = (int) $data->num + 1;
                $cost = '<a href="#" data-cost_id="' . $data->id .
                '" style="text-decoration:none;"  onclick="update_cost_modal(' .
                $data->Icost . ', ' . $data->Icost . ',\'' . $data->ptype . '\',' .
                $val . ', ' . $data->id . ')"  id="cost_' . $val . '">' .
                number_format($data->Icost / 100, 2) . ' </a>';
                return $cost;
            })->addColumn('costvalue', function ($data) {
                $val = (int) $data->num + 1;

                $span = '<span id="costvalue_' . $val . '" >' .
                number_format($data->Icostvalue / 100, 2) . ' </span>';
                return $span;
            })->escapeColumns([])->make(true);
        } catch (Exception $e) {
            return [
                "message" => $e->getMessage(),
                "error" => true,
            ];
        }

    }

    public function get_product_id(Request $request)
    {

        try {
            $search_string = $request->barcode;

            $is_matrix = false;
            $barcode = DB::table('productbarcode')->where('barcode', $search_string)->whereNull('deleted_at')->first();

            if (empty($barcode)) {
                $barcode = DB::table('productbmatrixbarcode')->where('bmatrixbarcode', $search_string)->whereNull('deleted_at')->first();
                $is_matrix = true;
            }

            $product_id = $barcode->product_id ?? null;

            // If product is from prd_inventory
            $product = DB::table('product')->select('product.*', 'locationproduct.quantity as qty')->join('locationproduct', 'locationproduct.product_id', 'product.id')->where('product.id', $product_id)->whereNull('product.deleted_at')->first();

            // If product is from prd_openitem
            if (empty($product)) {
                $product = DB::table('product')->select('product.*', 'prd_openitem.qty as qty')->join('prd_openitem', 'prd_openitem.product_id', 'product.id')->where('product.id', $product_id)->whereNull('product.deleted_at')->first();
            }
            $all_product = $this->get_product();
            foreach ($all_product as $p) {
                # code...
                if ($p->id == $product->id) {
                    return $p->num + 1;
                }
            }

        } catch (Exception $e) {
            Log::error([
                "Error: " => $e->getMessage(),
                "Line: " => $e->getLine(),
                "File: " => $e->getFile(),
            ]);

            return response()->json([
                "message" => "Barcode not found",
                "error" => true,
            ]);

            //abort(404);
        }
    }
}
