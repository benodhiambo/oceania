<?php

namespace App\Http\Controllers;

use App\Classes\FuelUsageExport;
use App\Exports\InvoicesExport;
use App\Models\CommReceipt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use PDF;
use App\Models\Company;

class CStoreSalesReportController extends Controller
{
    //
    public function printPDF(Request $request){
        Log::debug('Request: '.json_encode($request->all()));
        $company = Company::first();
        $location = DB::table('location')->first();
        $currency = $company->currency->code ?? 'MYR';
        //Change date Format
        $requestValue = $request->all();

        $start = date('Y-m-d', strtotime($request->start_date));
        $stop = date('Y-m-d', strtotime($request->end_date));

        Log::debug('Start Date: '.$start);
        Log::debug('Stop Date: '.$stop);

        $product_details= [];

        $products = DB::table('prd_inventory')->
            join('product', 'product.id', '=', 'prd_inventory.product_id')->
            whereNull('prd_inventory.deleted_at')->
            get();

        foreach($products as $product){

            $item_amount = 	DB::table('cstore_itemdetails')
                ->join('cstore_receiptproduct', 'cstore_receiptproduct.id', '=', 'cstore_itemdetails.receiptproduct_id')
                ->join('cstore_receipt', 'cstore_receiptproduct.receipt_id','=','cstore_receipt.id')
                ->where('cstore_receiptproduct.product_id', '=', $product->id)
                ->where('cstore_receipt.status', '!=', 'voided')
                ->whereBetween('cstore_itemdetails.created_at', [$start.' 00:00:00', $stop.' 23:59:59'])
                ->sum('cstore_itemdetails.amount');


            $quantity = 	DB::table('cstore_receiptdetails')
                ->join('cstore_receiptproduct', 'cstore_receiptproduct.receipt_id', '=', 'cstore_receiptdetails.receipt_id')
                ->join('cstore_receipt', 'cstore_receiptproduct.receipt_id','=','cstore_receipt.id')
                ->where('cstore_receipt.status', '!=', 'voided')
                ->where('cstore_receiptproduct.product_id', '=', $product->id)
                ->whereBetween('cstore_receiptdetails.created_at', [$start.' 00:00:00', $stop.' 23:59:59'])
                ->sum('quantity');

            $rec_price = 	DB::table('cstore_receiptdetails')
                ->join('cstore_receiptproduct', 'cstore_receiptproduct.receipt_id', '=', 'cstore_receiptdetails.receipt_id')
                ->join('cstore_receipt', 'cstore_receiptproduct.receipt_id','=','cstore_receipt.id')
                ->join('localprice','localprice.product_id','=','cstore_receiptproduct.product_id')
                ->where('cstore_receipt.status', '!=', 'voided')
                ->where('cstore_receiptproduct.product_id', '=', $product->id)
                ->whereBetween('cstore_receiptdetails.created_at', [$start.' 00:00:00', $stop.' 23:59:59'])
                ->sum('localprice.recommended_price');

            $tax = 	DB::table('cstore_receiptdetails')
                ->join('cstore_receiptproduct', 'cstore_receiptproduct.receipt_id', '=', 'cstore_receiptdetails.receipt_id')
                ->join('cstore_receipt', 'cstore_receiptproduct.receipt_id','=','cstore_receipt.id')
                ->join('localprice','localprice.product_id','=','cstore_receiptproduct.product_id')
                ->where('cstore_receipt.status', '!=', 'voided')
                ->where('cstore_receiptproduct.product_id', '=', $product->id)
                ->whereBetween('cstore_receiptdetails.created_at', [$start.' 00:00:00', $stop.' 23:59:59'])
                ->sum('cstore_receipt.service_tax');

			//->sum('receiptrefund.refund_amount');

            if($item_amount > 0) {
				$product->item_amount = $item_amount;// - ($refund * 100);
                $product->quantity = $quantity;
                $product->rec_price = $rec_price;
                $product->quantity = $quantity;
                $product->tax = $tax;

                Log::debug('Product: '.json_encode($product));
                $product_details[] = $product;
            }
        }

        $products = DB::table('prd_openitem')->
            join('product', 'product.id', '=', 'prd_openitem.product_id')->
            whereNull('prd_openitem.deleted_at')->
            get();

        foreach($products as $product){

            $item_amount = 	DB::table('cstore_itemdetails')
                ->join('cstore_receiptproduct', 'cstore_receiptproduct.id', '=', 'cstore_itemdetails.receiptproduct_id')
                ->join('cstore_receipt', 'cstore_receiptproduct.receipt_id','=','cstore_receipt.id')
                ->where('cstore_receiptproduct.product_id', '=', $product->id)
                ->where('cstore_receipt.status', '!=', 'voided')
                ->whereBetween('cstore_itemdetails.created_at', [$start.' 00:00:00', $stop.' 23:59:59'])
                ->sum('cstore_itemdetails.amount');


            $quantity = 	DB::table('cstore_receiptdetails')
                ->join('cstore_receiptproduct', 'cstore_receiptproduct.receipt_id', '=', 'cstore_receiptdetails.receipt_id')
                ->join('cstore_receipt', 'cstore_receiptproduct.receipt_id','=','cstore_receipt.id')
                ->where('cstore_receipt.status', '!=', 'voided')
                ->where('cstore_receiptproduct.product_id', '=', $product->id)
                ->whereBetween('cstore_receiptdetails.created_at', [$start.' 00:00:00', $stop.' 23:59:59'])
                ->sum('quantity');

            $rec_price = 	DB::table('cstore_receiptdetails')
                ->join('cstore_receiptproduct', 'cstore_receiptproduct.receipt_id', '=', 'cstore_receiptdetails.receipt_id')
                ->join('cstore_receipt', 'cstore_receiptproduct.receipt_id','=','cstore_receipt.id')
                ->join('localprice','localprice.product_id','=','cstore_receiptproduct.product_id')
                ->where('cstore_receipt.status', '!=', 'voided')
                ->where('cstore_receiptproduct.product_id', '=', $product->id)
                ->whereBetween('cstore_receiptdetails.created_at', [$start.' 00:00:00', $stop.' 23:59:59'])
                ->sum('localprice.recommended_price');

            $tax = 	DB::table('cstore_receiptdetails')
                ->join('cstore_receiptproduct', 'cstore_receiptproduct.receipt_id', '=', 'cstore_receiptdetails.receipt_id')
                ->join('cstore_receipt', 'cstore_receiptproduct.receipt_id','=','cstore_receipt.id')
                ->join('localprice','localprice.product_id','=','cstore_receiptproduct.product_id')
                ->where('cstore_receipt.status', '!=', 'voided')
                ->where('cstore_receiptproduct.product_id', '=', $product->id)
                ->whereBetween('cstore_receiptdetails.created_at', [$start.' 00:00:00', $stop.' 23:59:59'])
                ->sum('cstore_receipt.service_tax');

			//->sum('receiptrefund.refund_amount');

            if($item_amount > 0) {
				$product->item_amount = $item_amount;// - ($refund * 100);
                $product->quantity = $quantity;
                $product->rec_price = $rec_price;
                $product->quantity = $quantity;
                $product->tax = $tax;

                Log::debug('Product: '.json_encode($product));
                $product_details[] = $product;
            }
        }


		$refund = DB::table('cstore_receiptdetails')
			->join('cstore_receiptproduct', 'cstore_receiptproduct.receipt_id', '=', 'cstore_receiptdetails.receipt_id')
			->join('cstore_receipt', 'cstore_receiptproduct.receipt_id','=','cstore_receipt.id')
			->join('cstore_receiptrefund', 'cstore_receiptrefund.cstore_receipt_id', '=', 'cstore_receipt.id')
			->join('product','product.id','cstore_receiptproduct.product_id')
			->where('cstore_receipt.status', '!=', 'voided')
			->whereBetween('cstore_receiptdetails.created_at', [$start.' 00:00:00', $stop.' 23:59:59'])
			->select('product.name','product.systemid','cstore_receiptproduct.quantity','cstore_receiptrefund.refund_amount')
			->get();

		$receipt_refund = DB::table('cstore_receipt')
			->join('cstore_receiptrefund', 'cstore_receiptrefund.cstore_receipt_id', '=', 'cstore_receipt.id')
			->where('cstore_receipt.status', '!=', 'voided')
			->whereBetween('cstore_receipt.created_at', [$start.' 00:00:00', $stop.' 23:59:59'])
			->select('cstore_receipt.systemid','cstore_receiptrefund.refund_amount')
			->get();

        Log::debug('Sales: '.json_encode($product_details));

		$total_refund = DB::table('cstore_receiptdetails')
			->join('cstore_receiptproduct', 'cstore_receiptproduct.receipt_id', '=', 'cstore_receiptdetails.receipt_id')
			->join('cstore_receipt', 'cstore_receiptproduct.receipt_id','=','cstore_receipt.id')
			->join('cstore_receiptrefund', 'cstore_receiptrefund.cstore_receipt_id', '=', 'cstore_receipt.id')
			->where('cstore_receipt.status', '!=', 'voided')
			//->where('receiptproduct.product_id', '=', $product->id)
			->whereBetween('cstore_receiptdetails.created_at', [$start.' 00:00:00', $stop.' 23:59:59'])
			->sum('cstore_receiptrefund.refund_amount');

        $pdf = PDF::setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true])
			->loadView('sales_report.sales_report_pdf', compact('product_details', 'requestValue',
			  'total_refund' , 'location', 'refund', 'receipt_refund' , 'currency'));

        // download PDF file with download method
        // $pdf = PDF::loadHTML('<p>Hello World!!</p>');

        // return $pdf->stream();
        $pdf->getDomPDF()->setBasePath(public_path().'/');
        $pdf->getDomPDF()->setHttpContext(
            stream_context_create([
                'ssl' => [
                    'allow_self_signed'=> true,
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                ]
            ])
        );
        $pdf->setPaper('A4', 'portrait');
        return $pdf->download('SalesReport.pdf');
    }
}
