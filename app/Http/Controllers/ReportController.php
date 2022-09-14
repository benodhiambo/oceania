<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PDF;

class ReportController extends Controller
{
    //
    public function getReport()
    {
        $company = Company::first();
        $approvedDate = $company->approved_at;
        return view('report.report', ['approved_at' => $approvedDate]);
    }

    public function cstorePLPDF(Request $request)
    {
        Log::debug('Request: ' . json_encode($request->all()));
        $company = Company::first();
        $currency = $company->currency->code ?? 'MYR';

        //Change date Format
        $requestValue = $request->all();

        if (!$request->ev_start_date) {
            $request->ev_start_date = date('Y-m-d');
        }
        if (!$request->ev_end_date) {
            $request->ev_end_date = date('Y-m-d');
        }

        $start = date('Y-m-d', strtotime($request->ev_start_date));
        $stop = date('Y-m-d', strtotime($request->ev_end_date));

        Log::debug('Start Date: ' . $start);
        Log::debug('Stop Date: ' . $stop);

        $inventory_details = DB::select(DB::raw("
            SELECT
                p.id,
                p.systemid,
                p.name,
                cid.price /100 as price,
                crp.quantity as qty,
                lpc.cost/100 as cost,
                (( CAST(cid.price as SIGNED) /100  - CAST(lpc.cost as SIGNED) /100) *
                crp.quantity) as profit_loss,
                cr.created_at
            FROM
                cstore_receipt cr,
                cstore_receiptproduct crp,
                cstore_receiptdetails crd,
                product p,
                cstore_itemdetails cid,
                locationproduct_cost lpc,
                locprod_productledger lpl
            WHERE
                crp.product_id = p.id	 AND
                cr.id = crp.receipt_id  	AND
                cid.receiptproduct_id = crd.id  AND
                cr.status != 'voided'	AND
                lpc.locprodprodledger_id = lpl.id  AND
                lpl.product_systemid = p.systemid
            ;
        "));

        $openitem_details = DB::select(DB::raw("
            SELECT
                p.id,
                p.systemid,
                p.name,
                cid.price /100 as price,
                crp.quantity as qty,
                opc.cost /100 as cost,
                (( CAST(cid.price as SIGNED)/100 - CAST(opc.cost as SIGNED)/100) *
                crp.quantity) as profit_loss,
                cr.created_at

            FROM
                cstore_receipt cr,
                cstore_receiptproduct crp,
                cstore_receiptdetails crd,
                product p,
                cstore_itemdetails cid,
                openitem_cost opc,
                openitem_productledger opl

            WHERE
                crp.product_id = p.id	 AND
                cr.id = crp.receipt_id  	AND
                cid.receiptproduct_id = crd.id  AND
                cr.status != 'voided'	AND
                opc.openitemprodledger_id = opl.id AND
                opl.product_systemid = p.systemid
            ;
        "));

        $gotten_details = collect(array_merge(
            $inventory_details, $openitem_details))->
            whereBetween('created_at', [$start . ' 00:00:00', $stop . ' 23:59:59']);

        $filtered_collection = $this->products()->
            filter(function ($item) use ($gotten_details) {
            $ids = $gotten_details->pluck('id')->toArray();
            return !in_array($item->id, $ids);
        })->values();

        $report_details = $gotten_details->
            merge($filtered_collection)->values();
        $report_details = $this->collection_transformer($report_details);
        $location = Location::first();

        $report_details = $this->getAllProducts();

        $report_details = $report_details->map(function ($product) {
            return (object) [
                'id' => $product->id,
                'z_product_id' => $product->id,
                'systemid' => $this->getProductBarcode($product->id, $product->systemid),
                'name' => $product->name,
                'qty' => 0,
                'cost' => 0.00,
                'price' => 0.00,
                'profit_loss' => 0.00,
            ];
        });

        $pdf = PDF::setOptions(['isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true])->
            loadView(
            'report.cstore_profit_loss_pdf', compact(
                'currency',
                'report_details',
                'requestValue',
                'location')
        );

        $pdf->getDomPDF()->setBasePath(public_path() . '/');

        $pdf->getDomPDF()->setHttpContext(
            stream_context_create([
                'ssl' => [
                    'allow_self_signed' => true,
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                ],
            ])
        );

        $pdf->setPaper('A4', 'portrait');
        return $pdf->download('C-StoreProfitLoss.pdf');
    }

    public function getProductBarcode($id, $systemid)
    {
        $barcode = DB::table('productbarcode')
            ->where('product_id', $id)
            ->where('selected', 1)
            ->first();

        if (!is_null($barcode)) {
            return $barcode->barcode;
        } else {
            return $systemid;
        }
    }

    public function cost_value_reportPDF(Request $request)
    {
        Log::debug('Request: ' . json_encode($request->all()));
        $company = Company::first();
        $location = DB::table('location')->first();
        $currency = $company->currency->code ?? 'MYR';

        //Change date Format
        is_null($request->year) ? $start_d = date("Y-m-01") :
        $start_d = $request->year . '-' . $request->month . '-01';

        $requestValue = [
            'start_date' => date('Y-m-01', strtotime($start_d)),
            'end_date' => date('Y-m-t', strtotime($start_d)),
        ];

        $start = date('Y-m-01', strtotime($start_d));
        $end = date('Y-m-t', strtotime($start_d));
        $end_D = date('1970-Jan-01');
        $inventory_products = DB::table('prd_inventory')->
            select(
            'product.systemid',
            'product.name',
            'product.id',
            'locationproduct_cost.cost as Icost',
            'locationproduct.costvalue as Icostvalue',
            'localprice.recommended_price as Iprice',
            'locationproduct_cost.balance as  Iqty',
            DB::raw('null as barcode')
        )->
            leftjoin('product', 'product.id', '=', 'prd_inventory.product_id')->
            leftjoin('localprice', 'localprice.product_id', '=', 'prd_inventory.product_id')->
            leftjoin('locationproduct', 'locationproduct.product_id', '=', 'prd_inventory.product_id')->
            leftjoin('locprod_productledger', 'locprod_productledger.product_systemid', '=', 'product.systemid')->
            leftjoin('locationproduct_cost', 'locationproduct_cost.locprodprodledger_id', '=', 'locprod_productledger.id')->
            whereBetween('locprod_productledger.updated_at', [$end_D . ' 00:00:00', $end . ' 23:59:59'])->
            get();

        $openitem_products = DB::table('prd_openitem')->
            select(
            'product.systemid',
            'product.id',
            'product.name',
            'prd_openitem.costvalue as Icostvalue',
            'prd_openitem.price as Iprice',
            'openitem_cost.cost as Icost',
            'openitem_cost.balance as Iqty',
            DB::raw('null as barcode')
        )->
            leftjoin('product', 'product.id', '=', 'prd_openitem.product_id')->
            leftJoin('openitem_productledger', 'openitem_productledger.product_systemid', '=', 'product.systemid')->
            leftJoin('openitem_cost', 'openitem_cost.openitemprodledger_id', '=', 'openitem_productledger.id')->
            whereNotNull('openitem_cost.updated_at')->
            whereBetween('openitem_cost.updated_at', [$end_D . ' 00:00:00', $end . ' 23:59:59'])->
            get();

        $ivn_p = $this->collectionReceiver($inventory_products);
        $ivn_p = collect($ivn_p);
        $op_it = $this->collectionReceiver($openitem_products);
        $openitem_prod = $ivn_p->merge($op_it);
        foreach ($openitem_prod as $p) {
            # code...
            $p->barcode = $this->get_barcode($p->id);
        }
        $allproducts = $this->getAllProducts();

        $filtered_collection = $allproducts->filter(function ($item) use ($openitem_prod) {
            $ids = $openitem_prod->pluck('id')->toArray();
            return !in_array($item->id, $ids);
        })->values();

        $openitem_prod = $filtered_collection->merge($openitem_prod);

        $pdf = PDF::setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true])
            ->loadView('report.cost_value_rpt_pdf', compact(
                'openitem_prod',
                'requestValue',
                'location',
                'currency'
            ));

        // download PDF file with download method
        // $pdf = PDF::loadHTML('<p>Hello World!!</p>');
        // return $pdf->stream();
        $pdf->getDomPDF()->setBasePath(public_path() . '/');
        $pdf->getDomPDF()->setHttpContext(
            stream_context_create([
                'ssl' => [
                    'allow_self_signed' => true,
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                ],
            ])
        );
        $pdf->setPaper('A4', 'portrait');
        return $pdf->download('CostValueReport.pdf');
    }

    public function getAllProducts()
    {
        $inventory_products = DB::table('prd_inventory')->
            leftjoin('product', 'product.id', '=', 'prd_inventory.product_id')->
            leftjoin('localprice', 'localprice.product_id', '=', 'prd_inventory.product_id')->
            leftjoin('locationproduct', 'locationproduct.product_id', '=', 'localprice.product_id')->
            leftjoin('stockreportproduct', 'stockreportproduct.product_id', '=', 'locationproduct.product_id')->

            select(
            'product.id as id',
            'product.systemid',
            'product.name as name',
            DB::raw('null as Iqty'),
            DB::raw('null as Icostvalue'),
            DB::raw('null as Iprice'),
            DB::raw('null as Icost'),
            DB::raw('null as barcode')

        )->get();

        $openitem_products = DB::table('prd_openitem')->
            leftjoin('product', 'product.id', '=', 'prd_openitem.product_id')->select(
            'product.id as id',
            'product.name as name',
            'product.systemid',
            DB::raw('null as Iqty'),
            DB::raw('null as Icostvalue'),
            DB::raw('null as Iprice'),
            DB::raw('null as Icost'),
            DB::raw('null as barcode')
        )->get();

        foreach ($openitem_products as $key => $op) {
            $op->barcode = $this->get_barcode($op->id) ?? $op->systemid;
        }

        foreach ($inventory_products as $key => $op) {
            $op->barcode = $this->get_barcode($op->id) ?? $op->systemid;
        }

        $data = $inventory_products->
            merge($openitem_products)->unique('id');
        return $data;
    }

    public function collectionReceiver($collect)
    {
        $new_arr = collect();
        foreach ($collect->values()->all() as $key => $value) {
            # code...
            if ($new_arr->where('id', $value->id)->
                where('Icost', $value->Icost)->count() > 0) {
                # code...
                $h = $new_arr->where('id', $value->id)->
                    where('Icost', $value->Icost)->first();

                $h->Iqty += $value->Iqty;
                continue;
                //         // if (!is_null($openitemcost_record)) {
                //         //     $openitemcost = $openitemcost_record;
                //         //     $openitemcost_id = $openitemcost->openitemcost_id;
                $new_arr->push($value);
            }
            return $new_arr;
        }
    }

    public function collection_transformer($collect)
    {
        $new_arr = collect();
        foreach ($collect->values()->all() as $key => $value) {
            # code...
            if ($new_arr->where('id', $value->id)->
                where('price', $value->price)->count() > 0) {
                # code...
                $h = $new_arr->where('id', $value->id)->where('price', $value->price)->where('cost', $value->cost)->first();
                if ($h) {
                    $h->qty += $value->qty;
                    $h->profit_loss += $value->profit_loss;
                }

                continue;
            }
            $new_arr->push($value);
        }
        return $new_arr;
    }
    public function get_barcode($id)
    {

        $productbarcode = DB::select(
            // DB::raw("
            //     select JSON_ARRAYAGG(b.barcode) as barcode from  productbarcode b, product p where p.id = b.product_id  and p.id ='" . $id . "' and b.selected = 1 and b.deleted_at is null;
            // ")
            DB::raw("
            select b.barcode as barcode from  productbarcode b, product p where p.id = b.product_id  and p.id ='" . $id . "' and b.selected = 1 and b.deleted_at is null;
            ")
        );

        if (sizeof($productbarcode) > 0) {
            return $productbarcode[0]->barcode;
        } else {
            return null;
        }

    }

    public function products()
    {
        $inventory_details = DB::table('prd_inventory')->
            leftjoin('locationproduct', 'locationproduct.product_id', '=', 'prd_inventory.product_id')->
            leftjoin('product', 'locationproduct.product_id', '=', 'product.id')->
            leftjoin('localprice', 'prd_inventory.product_id', '=', 'localprice.product_id')->
            leftjoin('locprod_productledger', 'locprod_productledger.product_systemid', '=', 'product.systemid')->
            leftjoin('locationproduct_cost', 'locationproduct_cost.locprodprodledger_id', '=',
            'locprod_productledger.id')->

            groupBy(
            'product.id',
            'product.systemid',
            'product.name',
            'localprice.recommended_price',
            'locationproduct_cost.qty_out',
            'locationproduct_cost.cost'
        )->
            select(
            'product.id as id',
            'product.systemid as systemid',
            'product.name as name',
            DB::raw('null as price'),
            DB::raw('null as cost'),
            DB::raw('null as qty'),
            DB::raw('null as profit_loss')
        )->get();

        $openitem_details = DB::table('prd_openitem')->
            leftjoin('product', 'prd_openitem.product_id', '=', 'product.id')->
            leftjoin('openitem_productledger', 'openitem_productledger.product_systemid', '=', 'product.systemid')->
            leftjoin('openitem_cost', 'openitem_cost.openitemprodledger_id', '=', 'openitem_productledger.id')->
            groupBy('product.id', 'product.systemid', 'product.name', 'prd_openitem.price', 'openitem_cost.qty_out', 'openitem_cost.cost')->
            select(
            'product.id as id',
            'product.systemid as systemid',
            'product.name  as name',
            DB::raw('null as price'),
            DB::raw('null as cost'),
            DB::raw('null as qty'),
            DB::raw('null as profit_loss')
        )->get();

        $data = $inventory_details->merge($openitem_details);
        return $data;
    }
}
