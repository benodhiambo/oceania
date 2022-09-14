<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Illuminate\Support\Facades\Log;
use stdClass;

class CstoreExport implements FromView, WithEvents, WithCustomValueBinder, ShouldAutoSize
{
    protected $request;
    protected $tableHeaderLength = 12;

    function __construct($request)
    {
        $this->request = $request;
    }

    public function view(): View
    {
        if (is_null($this->request)) {
            $start = date("Y-m-01");
            $stop = date("Y-m-t");
        } else {
            $start = date('Y-m-d', strtotime($this->request->cstore_start_date));
            $stop = date('Y-m-d', strtotime($this->request->cstore_end_date));
        }

        $product = DB::select(DB::raw("
            SELECT
                cr.id,
                u.systemid as staff_id,
                u.fullname as staff_name,
                cr.systemid as receipt_i,
                cr.status,
                rpd.name as product_name,
                rpd.quantity,
                rpd.price,
                id.rounding,
                id.amount,
                rd.item_amount,
                rd.total,
                rd.wallet,
                rd.change,
                rd.creditcard,
                rd.void,
                rd.cash_received,
                rd.created_at as date,
                nullif(rf.refund_amount,0) as refund
            FROM

                cstore_receiptproduct rpd,
                cstore_itemdetails id,
                cstore_receiptdetails rd,
                users u,
                cstore_receipt cr
            LEFT JOIN cstore_receiptrefund rf ON rf.cstore_receipt_id = cr.id

            WHERE
                cr.id =rpd.receipt_id AND
                rpd.id = id.receiptproduct_id AND
                cr.id = rd.receipt_id AND
                cr.staff_user_id = u.id
            ;
        "));

        $prod_i = DB::table('prd_inventory')
            ->leftJoin('product', 'product.id', '=', 'prd_inventory.product_id')
            ->select('product.name as name')
            ->groupBy('name')
            ->get();
        $prod_o   = DB::table('prd_openitem')
            ->leftJoin('product', 'product.id', '=', 'prd_openitem.product_id')
            ->select('product.name as name')
            ->groupBy('name')
            ->get();

        $products = $prod_i->merge($prod_o);
        $product = collect($product);

        $data = $this->collection_transformer($product)->
            whereBetween('date', [$start. ' 00:00:00', $stop. ' 23:59:59']);
        
        $this->tableHeaderLength += count($products);
        return view('excel_export.cstore_receiptlist_excel', [
            'cstore_products' => $data,
            'product_name' => $products,
            'start_date' => $start,
            'stop_date' => $stop
        ]);
    }
    /**
     * Write code on Method
     *
     * @return response()
     */
    public function registerEvents(): array
    {

        return [

            AfterSheet::class    => function (AfterSheet $event) {

                $event->sheet->getDelegate()->
                    getRowDimension('1')->
                    setRowHeight(18);

                $event->sheet->getDelegate()->
                    getStyle(static::columnLetter($this->tableHeaderLength))
                    ->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setARGB('000000');

                $event->sheet->getStyle(static::columnLetter(
                    $this->tableHeaderLength))->
                    getFont()->
                    setBold(true)->
                    getColor()->
                    setRGB('ffffff');
            },

        ];
    }
    public function bindValue(Cell $cell, $value)
    {
        $cell->setValueExplicit($value,
            \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);

        return true;
    }

    public static function columnLetter($c)
    {

        $c = intval($c);
        if ($c <= 0) return '';
        $letter = '';

        while ($c != 0) {
            $p = ($c - 1) % 26;
            $c = intval(($c - $p) / 26);
            $letter = chr(65 + $p) . $letter;
        }

        return 'A3:' . $letter . '3';
    }

    public function collection_transformer($collect)
    {
        $new_arr = collect();
        $data = new StdClass();
        $price = array();
        $product_name = array();
        foreach ($collect->values()->all() as $value) {
            # code...

            if ($new_arr->where('receipt_i', $value->receipt_i)->count() > 0) {
                # code...
                $data = $new_arr->where('receipt_i', $value->receipt_i)->first();
                if(!in_array($data->product_name, $product_name)){
                    array_push($product_name, $data->product_name);
                    array_push($price, $data->amount);
                }

                if ($data) {

                    $data->id = $value->id;
                    $data->staff_name = $value->staff_name;
                    $data->staff_id = $value->staff_id;
                    $data->receipt_i = $value->receipt_i;
                    $data->date = $value->date;
                    $data->status = $value->status;
                    $data->quantity = $value->quantity;
                    array_push( $price, $value->amount);
                    $data->refund = $value->refund;
                    array_push($product_name,$value->product_name);
                    $data->total = $value->total;
                    $data->rounding = $value->rounding;
                    $data->item_amount = $value->item_amount;
                    $data->change = $value->change;
                    $data->cash_received = $value->cash_received;
                    $data->wallet = $value->wallet;
                    $data->creditcard = $value->creditcard;
                    $data->void = $value->void;
                }

                continue;

            }else{
                $new_arr->push($value);
            }
            $data->price = $price;
            $data->product_name = $product_name;
            $price = array();
            $product_name = array();
        }

        return $new_arr;
    }
}
