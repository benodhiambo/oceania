<?php

namespace App\Http\Controllers;

use App\Classes\SystemID;
use App\Models\Company;
use App\Models\Location;
use App\Models\Product;
use App\Models\PrdOpenitem;
use Illuminate\Http\Request;
use DB;
use Log;

class CentralStockMgmtController extends Controller
{
    public function qtyAvailable($prd_id)
    {
        try {
            $product_stock = DB::table('stockreportproduct')->where('product_id', $prd_id)->get()->sum('quantity');

            $sales = DB::table('cstore_receipt')->
                select('cstore_receiptproduct.quantity as quantity')->
                join('cstore_receiptproduct', 'cstore_receipt.id',
                    'cstore_receiptproduct.receipt_id')->
                leftJoin('cstore_receiptdetails', 'cstore_receipt.id',
                    'cstore_receiptdetails.receipt_id')->
                orderBy('cstore_receipt.updated_at', "desc")->
                where("cstore_receiptproduct.product_id", $prd_id)->
                whereNotIn('cstore_receipt.status', ['voided'])->
                get()->
                sum('quantity');

            return $product_stock - $sales;
        } catch (\Exception $e) {
            \Log::info([
                "Error"    => $e->getMessage(),
                "File"    => $e->getFile(),
                "Line"    => $e->getLine()
            ]);
            abort(500);
        }
    }

    public function bookValAvailable($prd_id)
    {
        try {

            $product_stock = DB::table('fuelmovement')->join('prd_ogfuel', 'prd_ogfuel.id',
             'fuelmovement.ogfuel_id')->where('prd_ogfuel.product_id', $prd_id)->first();

            $receipt = DB::table('stockreportproduct')->leftjoin(
                    'stockreport',
                    'stockreport.id',
                    'stockreportproduct.stockreport_id'
                )->where('stockreportproduct.product_id', $prd_id)->get()->sum('quantity');

            return ($product_stock->book ?? 0) + $receipt ?? 0;
        } catch (\Exception $e) {
            \Log::info([
                "Error"    => $e->getMessage(),
                "File"    => $e->getFile(),
                "Line"    => $e->getLine()
            ]);
            abort(500);
        }
    }


    public function showProductledger(Request $request)
    {
        $product = DB::table('product')->where("id", $request->product_id)->first();

        $location = Location::first();
        $data = collect();
        // echo "<pre>";
        // print_r($product);
        // exit();

        DB::table('cstore_receipt')->
        select('cstore_receipt.*', 'cstore_receiptproduct.quantity as quantity', 'cstore_receiptdetails.id as receiptdetails_id')->
        join('cstore_receiptproduct', 'cstore_receipt.id', 'cstore_receiptproduct.receipt_id')->
            // join('locprod_productledger', 'locprod_productledger.product_id', 'cstore_receiptproduct.product_id')->
        leftJoin('cstore_receiptdetails', 'cstore_receipt.id', 'cstore_receiptdetails.receipt_id')->
        orderBy('cstore_receipt.updated_at', "desc")->
        where("cstore_receiptproduct.product_id", $request->product_id)->get()->
        map(function ($product) use ($data) {
            $packet = collect();
            $packet->id            = $product->id;
            $packet->status     = $product->status;
            $packet->systemid    = $product->systemid;
            $packet->quantity    = $product->quantity * -1;
            //$packet->cost       = $product->cost;
            $packet->created_at = $product->created_at;
            $packet->voided_at    = $product->voided_at;
            $packet->doc_type    = "Cash Sales";
            $data->push($packet);
        });

        DB::table('stockreportproduct')->
            leftjoin('stockreport', 'stockreport.id', 'stockreportproduct.stockreport_id')->
            // leftjoin('locprod_productledger', 'locprod_productledger.product_id', 'stockreportproduct.product_id')->
            where('stockreportproduct.product_id', $product->id)->
            orderBy('stockreport.updated_at', "desc")->get()->
            map(function ($product) use ($data) {
                $packet = collect();
                $packet->id            = $product->id;
                $packet->status     = $product->status;
                $packet->systemid    = $product->systemid;
                $packet->quantity    = $product->quantity;
                // $packet->cost   	= $product->cost;
                $packet->created_at = $product->created_at;
                $packet->voided_at    = $product->voided_at ?? "";
                $packet->doc_type    = ucfirst($product->type);
                $data->push($packet);
            });

        $data = $this->add_cost_to_prd_ledger($data, $product->id);

        $data = $data->sortByDesc('created_at')->values();

        Log::debug(['**showProductLedger**: $data=' => $data]);

        /* Here is where you store $data in new productledger schema:
		   locprod_productledger */


        return view(
            "inv_stockmgmt.productledger",
            compact("location", "product", "data")
        );
    }


    public function add_cost_to_prd_ledger($data, $prd_id)
    {

        $prd_info = DB::table('product')->
                    whereId($prd_id)->
                    first();

        Log::debug("add_cost_to_prd_ledger: product_id=".$prd_id);
        Log::debug("add_cost_to_prd_ledger: prd_info=".json_encode($prd_info));
        Log::debug("add_cost_to_prd_ledger: data=".json_encode($data));

        $new_data = collect();
        Log::debug("add_cost_to_prd_ledger: new_data=" . json_encode($new_data));
        foreach ($data as $prd) {

            $cost = DB::table('locprod_productledger')->
            where('product_systemid', $prd_info->systemid)->
            where('qty', $prd->quantity)->
            where('stockreport_id', $prd->id)->
            orderBy('created_at', 'desc')->
            first();



            if (!empty($cost)) {
                $prd->cost = $cost->cost;
            } else {

                $prd->cost = 0;
            }

            $new_data->push($prd);
            $this->reflect_autostock_locationproduct_cost();
        }

        return $new_data;
    }


    function stockUpdate(Request $request)
    {
        Log::debug('****stockUpdate()*****');
        try {
            $user_id = \Auth::user()->id;
            $table_data = $request->get('table_data');
            $stock_type = $request->get('stock_type');
            $stock_system = new SystemID("stockreport");

            $company = Company::first();
            $location = Location::first();

            foreach ($table_data as $key => $value) {
                Log::debug('***stockUpdate()*** $value=' . json_encode($value));
                //if qty zero
                if ($value['qty'] <= 0)
                    continue;

                //If SI or SO
                if ($stock_type == "IN") {
                    $curr_qty     = $value['qty'];
                    $type        =  'stockin';
                } else {
                    $curr_qty  = $value['qty'] * -1;
                    $type       = 'stockout';
                }

                //Location Product
                $locationproduct = DB::table('locationproduct')->where([
                    'product_id' => $value['product_id']
                ])->first();

                if ($locationproduct) { // modify existing location product

                    $locationproduct = DB::table('locationproduct')->where([
                        'product_id'                => $value['product_id']
                    ])->increment('quantity', $curr_qty);
                } else {
                    DB::table('locationproduct')->insert([
                        "location_id"        =>    $location->id,
                        "product_id"        =>    $value['product_id'],
                        "quantity"            =>    $curr_qty,
                        "damaged_quantity"    =>    0,
                        "created_at"        =>    date('Y-m-d H:i:s'),
                        'updated_at'        =>  date('Y-m-d H:i:s'),
                    ]);
                }

                //Stock Report
                $stockreport_id = DB::table('stockreport')->insertGetId([
                    'systemid'            =>    $stock_system,
                    'creator_user_id'    =>    $user_id,
                    'type'                =>    $type,
                    'location_id'        =>    $location->id,
                    "created_at"        =>    date('Y-m-d H:i:s'),
                    'updated_at'        =>    date('Y-m-d H:i:s')
                ]);

                DB::table('stockreportproduct')->insert([
                    "stockreport_id"    =>    $stockreport_id,
                    "product_id"        =>    $value['product_id'],
                    "quantity"            =>    $curr_qty,
                    "created_at"        =>    date('Y-m-d H:i:s'),
                    'updated_at'        =>    date('Y-m-d H:i:s')
                ]);

                $prd = DB::table('product')->whereId($value['product_id'])->first();

                if ($stock_type == "IN") {
                    $latest_cost = DB::table('locprod_productledger')->where('product_systemid', $prd->systemid)->whereIn('type', ['stockin', 'received'])->whereNotNull('cost')->orderBy('created_at', 'desc')->first();

                    $cost = empty($latest_cost) ? 0 : $latest_cost->cost;

                    $locprodid = DB::table('locprod_productledger')->insertGetId([
                            "stockreport_id"    =>    $stockreport_id,
                            "product_systemid"    =>    $prd->systemid,
                            "qty"                =>    $curr_qty,
                            "cost"                =>    $cost,
                            "last_update"        =>    date('Y-m-d H:i:s'),
                            "status"            =>    'active',
                            "type"                =>    $type,
                            "deleted_at"        =>    NULL,
                            "created_at"        =>    date('Y-m-d H:i:s'),
                            "updated_at"        =>    date('Y-m-d H:i:s')
                        ]);

                    $lp_costid = DB::table('locationproduct_cost')->insertGetId([
                            "locprodprodledger_id"    =>    $locprodid,
                            "qty_in"                =>    $curr_qty,
                            "qty_out"                =>    0,
                            "balance"                 =>    $curr_qty,
                            "cost"                    =>    $cost,
                            "deleted_at"            =>    NULL,
                            "created_at"            =>    date('Y-m-d H:i:s'),
                            "updated_at"            =>    date('Y-m-d H:i:s')
                        ]);
                } else if ($stock_type == "OUT") {
                    $earliest_cost = DB::table('locprod_productledger')->where('product_systemid', $prd->systemid)->whereNotNull('cost')->orderBy('created_at', 'asc')->first();

                    $cost = empty($earliest_cost) ? 0 : $earliest_cost->cost;


                    $openitemprodid = DB::table('openitem_productledger')->insertGetId([
                            "stockreport_id"    =>    $stockreport_id,
                            "product_systemid"    =>    $prd->systemid,
                            "qty"                =>    $curr_qty,
                            "cost"                =>    $cost,
                            "last_update"        =>    date('Y-m-d H:i:s'),
                            "status"            =>    'active',
                            "type"                =>    $type,
                            "deleted_at"        =>    NULL,
                            "created_at"        =>    date('Y-m-d H:i:s'),
                            "updated_at"        =>    date('Y-m-d H:i:s')
                        ]);

                    \Log::debug('******THRU THIS STCOKOUT*****');
                    $this->process_locprod_stockout($prd->systemid, $curr_qty);
                }

                PrdOpenitem::where('product_id', $value['product_id'])->get()->map(function ($f) {
                    $f->qty = app("App\Http\Controllers\CentralStockMgmtController")->qtyAvailable($f->product_id);
                    $f->update();
                });
            }
            return response()->json(["status"    =>    true]);
        } catch (\Exception $e) {
            \Log::info([
                "Error"    => $e->getMessage(),
                "File"    => $e->getFile(),
                "Line"    => $e->getLine()
            ]);
            abort(500);
        }
    }

    public function process_locprod_stockout($systemid, $curr_qty)
    {

        try {

            // Get oldest non zero balance -- 1st Level
            $oldest_bal = DB::table('locationproduct_cost')->join(
                    'locprod_productledger',
                    'locprod_productledger.id',
                    'locationproduct_cost.locprodprodledger_id'
                )->select(
                    'locprod_productledger.id as ledger_id',
                    'locprod_productledger.stockreport_id as sr_id',
                    'locprod_productledger.type as doc_type',
                    'locationproduct_cost.cost as cost',
                    'locationproduct_cost.id as id',
                    'locationproduct_cost.qty_in as qty_in',
                    'locationproduct_cost.qty_out as qty_out',
                    'locationproduct_cost.balance as balance',
                    'locationproduct_cost.created_at as created_at',
                    'locationproduct_cost.updated_at as updated_at'
                )->where("locprod_productledger.product_systemid", $systemid)->where('locationproduct_cost.balance', '>', 0)->orderBy('locationproduct_cost.created_at', 'asc')->first();

            $cost = empty($oldest_bal) ? 0 : $oldest_bal->cost;

            if (!empty($oldest_bal)) {

                $compare = $curr_qty;

                if ($oldest_bal->balance >= ($compare * -1)) {

                    DB::table('locationproduct_cost')->
                        whereId($oldest_bal->id)->
                        update([
                            "qty_out"        =>    $curr_qty + $oldest_bal->qty_out,
                            "balance"        =>    $oldest_bal->qty_in + ($curr_qty + $oldest_bal->qty_out),
                            "updated_at"    =>    date('Y-m-d H:i:s')
                 ]);

                } else {
                    $carry_over_bal = $curr_qty + $oldest_bal->balance;

                    DB::table('locationproduct_cost')->whereId($oldest_bal->id)->update([
                            "qty_out"        =>    $oldest_bal->qty_in * -1,
                            "balance"        =>    0,
                            "updated_at"    =>    date('Y-m-d H:i:s')
                        ]);

                    $this->process_locprod_stockout($systemid, $carry_over_bal);
                }
            }

            $this->create_receiptcost();

        } catch (\Exception $e) {
            \Log::info([
                "Error"    => $e->getMessage(),
                "File"    => $e->getFile(),
                "Line"    => $e->getLine()
            ]);
            abort(500);
        }
    }


    public function autoStockIn($product_id, $qty)
    {

        try {

            Log::debug("Auto Stock In - Product ID: " . $product_id . " - Qty: " . $qty);

            $user_id = \Auth::user()->id;
            $stock_system = new SystemID("stockreport");

            $company = Company::first();
            $location = Location::first();

            $type        =  'stockin';
            //Location Product
            $locationproduct = DB::table('locationproduct')->where([
                'product_id' => $product_id
            ])->first();

            if ($locationproduct) { // modify existing location product

                $locationproduct = DB::table('locationproduct')->where([
                    'product_id'                => $product_id
                ])->increment('quantity', $qty);
            } else {
                DB::table('locationproduct')->insert([
                    "location_id"        =>    $location->id,
                    "product_id"        =>    $product_id,
                    "quantity"            =>    $qty,
                    "damaged_quantity"    =>    0,
                    "created_at"        =>    date('Y-m-d H:i:s'),
                    'updated_at'        =>  date('Y-m-d H:i:s'),
                ]);
            }

            //Stock Report
            $stockreport_id = DB::table('stockreport')->insertGetId([
                'systemid'            =>    $stock_system,
                'creator_user_id'    =>    $user_id,
                'type'                =>    $type,
                'location_id'        =>    $location->id,
                "created_at"        =>    date('Y-m-d H:i:s'),
                'updated_at'        =>    date('Y-m-d H:i:s')
            ]);

            DB::table('stockreportproduct')->insert([
                "stockreport_id"    =>    $stockreport_id,
                "product_id"        =>    $product_id,
                "quantity"            =>    $qty,
                "created_at"        =>    date('Y-m-d H:i:s'),
                'updated_at'        =>    date('Y-m-d H:i:s')
            ]);

            PrdOpenitem::where('product_id', $product_id)->get()->map(function ($f) {
                $f->qty = app("App\Http\Controllers\CentralStockMgmtController")->
                    qtyAvailable($f->product_id);
                $f->update();
            });

            $this->create_receiptcost();

        } catch (\Exception $e) {
            \Log::info([
                "Error"    => $e->getMessage(),
                "File"    => $e->getFile(),
                "Line"    => $e->getLine()
            ]);
        }
    }

    public function showStockReport()
    {
        $stockreport = DB::table('stockreport')->
        join('stockreportproduct', 'stockreportproduct.stockreport_id', '=', 'stockreport.id')->
        join('product', 'product.id', '=', 'stockreportproduct.product_id')->
        where('stockreport.systemid', request()->report_id)->
        get();

        $stockreport_data = DB::table('stockreport')->
        select(
            'users.fullname as staff_name',
            'users.systemid as staff_id',
            'stockreport.systemid as document_no',
            'stockreport.id as stockreport_id',
            'stockreport.type as refund_type',
            'stockreport.created_at as last_update',
            'location.name as location',
            'location.id as locationid')->
        leftjoin('location', 'location.id', '=', 'stockreport.location_id')->
        join('users', 'users.id', '=', 'stockreport.creator_user_id')->
        where('stockreport.systemid', request()->report_id)->
        orderBy('stockreport.updated_at', "desc")->
        first();

        $isWarehouse = false;
        return view(
            'inv_stockmgmt.inventorystockreport',
            compact('stockreport', 'stockreport_data', 'isWarehouse')
        );
    }

    public function reflect_autostock_locationproduct_cost()
    {

        $locProd = DB::select(DB::raw("
            SELECT
                p.id as product_id,
                p.name,
                p.systemid,
                srp.quantity,
                srp.stockreport_id
            FROM
                product p,
                prd_inventory piv,
                stockreport sr,
                stockreportproduct srp
            WHERE
                p.id = piv.product_id AND
                srp.product_id = p.id AND
                srp.stockreport_id = sr.id
            ;")
        );

        if(!empty($locProd)){

            foreach ($locProd as $key => $value) {
                # code...
                $stocrep = DB::table('locprod_productledger')->
                    where('stockreport_id', $value->stockreport_id)->
                    where('product_systemid', $value->systemid)->
                    first();
                $product = DB::table('locprod_productledger')->
                    where('product_systemid', $value->systemid)->
                    latest()->
                    first();

                if(!$stocrep){

                    if ($product) {

                        $cost = DB::select(DB::raw("
                            SELECT
                                lpl.product_systemid,
                                max(lpc.cost) as locost
                            FROM
                                locprod_productledger lpl,
                                locationproduct_cost lpc
                            WHERE
                                lpc.locprodprodledger_id ='".$product->id."'
                            GROUP BY
                                lpl.product_systemid
                                ;"
                            )
                        );

                        $cost = $cost[0]->locost ?? 0;
                        Log::debug("LOCost: ".$cost);
                        if($value->quantity >= 0){
                            $lpl_id = DB::table('locprod_productledger')->insertGetID([
                                "stockreport_id"    =>    $value->stockreport_id,
                                "product_systemid"    =>    $value->systemid,
                                "qty"               =>    $value->quantity,
                                "cost"              =>    $cost,
                                "status"            =>    "active",
                                "type"              =>    "stockin",
                                "last_update"       =>    date('Y-m-d H:i:s'),
                                "created_at"        =>    date('Y-m-d H:i:s'),
                                'updated_at'        =>    date('Y-m-d H:i:s')
                            ]);

                            DB::table('locationproduct_cost')->insert([
                                "locprodprodledger_id"   =>  $lpl_id,
                                "cost"              =>    $cost,
                                "qty_in"         =>  $value->quantity,
                                "qty_out"        =>  $value->quantity * -1,
                                "balance"        => ($value->quantity * -1) + $value->quantity,
                                "created_at"     =>  date('Y-m-d H:i:s'),
                                "updated_at"     =>  date('Y-m-d H:i:s')
                            ]);
                        }

                    }else{

                        if($value->quantity >= 0){
                            $lpl_id = DB::table('locprod_productledger')->insertGetID([
                                "stockreport_id"    =>    $value->stockreport_id,
                                "product_systemid"    =>    $value->systemid,
                                "qty"               =>    $value->quantity,
                                "cost"              =>    0,
                                "status"            =>    "active",
                                "type"              =>    "stockin",
                                "last_update"       =>    date('Y-m-d H:i:s'),
                                "created_at"        =>    date('Y-m-d H:i:s'),
                                'updated_at'        =>    date('Y-m-d H:i:s')
                            ]);

                            DB::table('locationproduct_cost')->insert([
                                "locprodprodledger_id"   =>  $lpl_id,
                                "qty_in"         =>  $value->quantity,
                                "qty_out"        =>  $value->quantity * -1,
                                "balance"        => ($value->quantity * -1) + $value->quantity,
                                "created_at"     =>  date('Y-m-d H:i:s'),
                                "updated_at"     =>  date('Y-m-d H:i:s')
                            ]);
                        }
                    }
                }
            }

        }
    }

    public function create_receiptcost(){

        $receipt_id = DB::select(DB::raw("
            SELECT
                lpc.id as locprodcost_id,
                cr.id as csreceipt_id
            FROM
                locationproduct_cost lpc,
                locprod_productledger lpl,
                stockreport sr,
                stockreportproduct srp,
                cstore_receiptproduct crp,
                cstore_receipt cr
            WHERE
                lpc.locprodprodledger_id = lpl.id AND
                lpl.stockreport_id = srp.stockreport_id AND
                lpl.stockreport_id = sr.id AND
                srp.product_id = crp.product_id AND
                crp.receipt_id = cr.id
            ;")
        );

        if(!empty($receipt_id)){

            foreach ($receipt_id as $key => $value) {
                # code...
                $stocrep = DB::table('locprodcost_csreceipt')->
                    where('csreceipt_id', $value->csreceipt_id)->
                    where('locprodcost_id', $value->locprodcost_id)->
                    first();

                if(!$stocrep){

                    DB::table('locprodcost_csreceipt')->insert([
                        "csreceipt_id"   =>  $value->csreceipt_id,
                        "locprodcost_id" =>  $value->locprodcost_id,
                        "created_at"     =>  date('Y-m-d H:i:s'),
                        "updated_at"     =>  date('Y-m-d H:i:s')
                    ]);
                }
            }
        }
    }
}
