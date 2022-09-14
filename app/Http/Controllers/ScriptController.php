<?php

namespace App\Http\Controllers;

use App\Models\FuelReceiptList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ScriptController extends Controller
{
    public function copyFuelReceiptData(){

      $fuel_receipt_data = DB::table('fuel_receipt')->
    		selectRaw('fuel_receipt.status',
					'fuel_receipt.id',
					'fuel_receipt.systemid',
					'fuel_receipt.pump_no',
					'fuel_receiptdetails.total',
					'receiptfilled.filled',
					'fuel_receipt.id AS receipt_id',
					'fuel_receipt.created_at AS created_at',
					'fuel_receipt.updated_at AS updated_at')->
        join('fuel_receiptdetails', 'fuel_receiptdetails.receipt_id', 'fuel_receipt.id')->
        join('fuel_receiptproduct', 'fuel_receiptproduct.receipt_id', 'fuel_receipt.id')->
        leftjoin('authreceipt', 'authreceipt.receipt_id', 'fuel_receipt.id')->
        leftjoin('receiptfilled', 'receiptfilled.auth_systemid', 'authreceipt.auth_systemid')->
        whereNull('fuel_receipt.deleted_at')->
        orderBy('fuel_receipt.id', 'DESC')->get();
//        Loop through all data and copy to new one

        foreach($fuel_receipt_data as $fuel_receipt){

            $fuel_receipt_list_count = FuelReceiptList::where('fuel_receipt_id',$fuel_receipt->id)->count();
            if(!$fuel_receipt_list_count){
                DB::table('fuel_receiptlist')->insert([
                    "fuel_receipt_id" => $fuel_receipt->id,
                    "fuel_receipt_systemid" => $fuel_receipt->systemid,
                    "pump_no" => $fuel_receipt->pump_no,
                    "total" => !empty($fuel_receipt->total) ? $fuel_receipt->total : '0.00',
                    "fuel" => !empty($fuel_receipt->total) ? $fuel_receipt->total : '0.00',
                    "filled" => !empty($fuel_receipt->filled) ? $fuel_receipt->filled : '0.00',
                    "refund" =>!empty($fuel_receipt->total) ? $fuel_receipt->total - $fuel_receipt->filled : '0.00' ,
                    "status" => $fuel_receipt->status,
                    "created_at" => $fuel_receipt->created_at,
                    'updated_at' => $fuel_receipt->updated_at,
                ]);
            }
        }
    }


    public function seed_cost_productledger_table(){
        $stock_products_inventory=  DB::table('stockreport')->
            leftJoin('stockreportproduct','stockreportproduct.stockreport_id', 'stockreport.id')->
            leftjoin('product', 'product.id', '=', 'stockreportproduct.product_id')->
            leftjoin('localprice', 'localprice.product_id', '=', 'stockreportproduct.product_id')->
            where('product.ptype', 'inventory')->
            select(
                'stockreportproduct.product_id',
                'stockreportproduct.quantity',
                'stockreport.type',
                'stockreport.id as stockreport_id',
                'localprice.recommended_price as iprice',
                'product.systemid as product_systemid',
                'product.ptype as product_type',
                'stockreport.status as status',
                'stockreport.created_at as created_at',
                'stockreport.updated_at as updated_at',
                 DB::raw('null  as oprice')
            )->latest()->get();

        $stock_products_openitem =  DB::table('stockreport')->
            leftJoin('stockreportproduct', 'stockreportproduct.stockreport_id', 'stockreport.id')->
            leftjoin('product', 'product.id', '=', 'stockreportproduct.product_id')->
            leftjoin('prd_openitem', 'prd_openitem.product_id', '=', 'stockreportproduct.product_id')->
            where('product.ptype', 'openitem')->
            select(
                'stockreportproduct.product_id',
                'stockreportproduct.quantity',
                'stockreport.type',
                'stockreport.id as stockreport_id',
                'prd_openitem.price as oprice',
                'product.systemid as product_systemid',
                'product.ptype as product_type',
                'stockreport.status as status',
                'stockreport.created_at as created_at',
                'stockreport.updated_at as updated_at',
                DB::raw('null  as iprice'),

            )->get();

        Log::debug("stoseed_cost_productledger_table: stock_products_openitem=".
            json_encode($stock_products_openitem));
            
        $stock_products = $stock_products_inventory->
            merge($stock_products_openitem);
/*
        Log::debug("seed_cost_productledger_table: inventory products=" .
            json_encode($stock_products));
 */
        foreach($stock_products as $stock_product){
            $stock_product_inven_exist = DB::table('locprod_productledger')->
                where('stockreport_id',$stock_product->stockreport_id)->
                where('created_at',$stock_product->created_at)->
                where('updated_at', $stock_product->updated_at)->
                first();
            $stock_product_openitem_exist = DB::table('openitem_productledger')->
                where('stockreport_id', $stock_product->stockreport_id)->
                where('created_at', $stock_product->created_at)->
                where('updated_at',$stock_product->updated_at)->first();


            if($stock_product->product_type == 'inventory' && !$stock_product_inven_exist){
                 $loc= DB::table('locprod_productledger')->
                 insertGetId([
                    "product_systemid" => $stock_product->product_systemid,
                    "qty" => $stock_product->quantity,
                    "type" => $stock_product->type,
                    'cost' => round($stock_product->iprice * 0.75),
                    "stockreport_id" => $stock_product->stockreport_id,
                    "status" => 'active',
                    "last_update" => $stock_product->created_at,
                    'updated_at' => $stock_product->updated_at,
                    'created_at'=> $stock_product->created_at,
                ]);

                $old_qty = DB::table('locationproduct_cost')->
                	where('locprodprodledger_id',$loc)->first();

               /*  Log::debug("seed_cost_productledger_table:=" .
              		json_encode($stock_products)); */


                if($stock_product->quantity > 0){
                    DB::table('locationproduct_cost')->
                    insert([
                        "locprodprodledger_id" => $loc,
                        "cost"=> round($stock_product->iprice * 0.75),
                        "qty_in"=>$stock_product->quantity,
                        "qty_out"=>0,
                        "balance" => empty($old_qty->balance) ? $stock_product->quantity :
                            $old_qty->balance  + $stock_product->quantity,
                        'updated_at' => $stock_product->updated_at,
                        'created_at' => $stock_product->created_at,
                    ]);

                }else{
                    DB::table('locationproduct_cost')->
                    insert(["locprodprodledger_id" => $loc,
                        "cost" => round($stock_product->iprice * 0.75),
                        "qty_in" => 0,
                        "qty_out" => $stock_product->quantity,
                        "balance" => empty($old_qty->balance) ? $stock_product->quantity :
                            $old_qty->balance  + $stock_product->quantity,
                        'updated_at' => $stock_product->updated_at,
                        'created_at' => $stock_product->created_at,
                    ]);
                }
            }elseif($stock_product->product_type == 'openitem' && !$stock_product_openitem_exist){

                Log::debug("seed_cost_productledger_table: openitem product exist=" .
                    json_encode($stock_product));

                $openitem_id = DB::table('openitem_productledger')->
                insertGetId([
                    "product_systemid" => $stock_product->product_systemid,
                    "qty" => $stock_product->quantity,
                    "type" => $stock_product->type,
                    "stockreport_id" => $stock_product->stockreport_id,
                    "status" => 'active',
                    'cost' => round($stock_product->oprice * 0.75),
                    "last_update" => $stock_product->created_at,
                    'updated_at' => $stock_product->updated_at,
                    'created_at'=> $stock_product->created_at,
                ]);
                /*  Log::debug("seed_cost_productledger_table $openitem_id:=" .
                        json_encode($openitem_id));
                 */
                $old_balance = DB::table('openitem_cost')->
                    where('openitemprodledger_id',$openitem_id)->first()->balance ?? 0;

                if($stock_product->quantity > 0){
                    DB::table('openitem_cost')->
                    insert([
                        "openitemprodledger_id" => $openitem_id,
                        "cost"=> round($stock_product->oprice * 0.75),
                        "qty_in"=>$stock_product->quantity,
                        "qty_out"=>0,
                        "balance"=> empty($old_balance) ? $stock_product->quantity :
                            $old_balance + $stock_product->quantity,
                        'updated_at' => $stock_product->updated_at,
                        'created_at' => $stock_product->created_at,
                    ]);
                }else{
                    DB::table('openitem_cost')->
                    insert([
                        "openitemprodledger_id" => $openitem_id,
                        "cost" => round($stock_product->oprice * 0.75),
                        "qty_in" => 0,
                        "qty_out" => $stock_product->quantity,
                        "balance" =>  empty($old_balance) ? $stock_product->quantity :
                            $old_balance  + $stock_product->quantity,
                        'updated_at' => $stock_product->updated_at,
                        'created_at' => $stock_product->created_at,
                    ]);
                }
            }

        }

        if(!empty($stock_products)){
           return response()->json(['success'=> 'tables cost seeded successfully', 'status'=>200]);
        }

    }
}
