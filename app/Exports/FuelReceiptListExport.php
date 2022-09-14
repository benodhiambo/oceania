<?php

namespace App\Exports;


use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;

use PhpOffice\PhpSpreadsheet\Cell\Cell;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;
use PhpOffice\PhpSpreadsheet\Cell\DataType;


class FuelReceiptListExport extends DefaultValueBinder implements FromView, WithHeadings, WithEvents, WithCustomValueBinder, ShouldAutoSize
{
    protected $request;
    protected $tableHeaderLength = 17;

    function __construct($request)
    {
        $this->request = $request;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function view(): View
    {
        // dd($this->request->all());
        // $requestValue = $this->request->all();
        if (is_null($this->request)) {
            $start = date("Y-m-01");
            $stop = date("Y-m-t");
        } else {
            $start = date('Y-m-d', strtotime($this->request->fuel_start_date));
            $stop = date('Y-m-d', strtotime($this->request->fuel_end_date));
        }

        $data = DB::table('fuel_receipt')->
            leftJoin('fuel_receiptdetails', 'fuel_receiptdetails.receipt_id', '=', 'fuel_receipt.id')->
            leftJoin('fuel_receiptlist', 'fuel_receiptlist.fuel_receipt_id', '=', 'fuel_receipt.id')->
            leftJoin('fuel_receiptproduct', 'fuel_receiptproduct.receipt_id', '=', 'fuel_receipt.id')->
            leftJoin('users', 'users.id', '=', 'fuel_receipt.staff_user_id')->
            whereBetween('fuel_receiptlist.created_at', [$start . ' 00:00:00', $stop . ' 23:59:59'])->
            select(

                'fuel_receipt.id',
                'users.systemid as staff_id',
                'users.fullname as staff_name',
                'fuel_receipt.systemid as receipt_i',
                'fuel_receiptlist.fuel_receipt_tstamp as date',
                'fuel_receiptlist.pump_no as pump_no',
                'fuel_receiptlist.fuel as fuel',
                'fuel_receiptlist.filled as filled',
                'fuel_receiptlist.refund as refund',
                'fuel_receiptlist.newsales_rounding as newsales_rounding',
                'fuel_receipt.status as status',
                'fuel_receiptproduct.quantity as quantity',
                'fuel_receiptproduct.price as price',
                'fuel_receiptproduct.name as product_name',
                'fuel_receiptdetails.*'
            )->get();
        $products = DB::table('fuel_receiptproduct')
            ->select('name')
            ->groupBy('name')
            ->get();

        $this->tableHeaderLength += count($products) * 2;
        return view('excel_export.fuel_receiptlist_excel', [
            'receiptList' => $data,
            'product_name' => $products,
            'start_date' => $start,
            'stop_date' => $stop
        ]);
    }
    /**
     * @return array
     */

    /**
     * Write code on Method
     *
     * @return response()
     */

    public function headings(): array
    {
        return [

            [
                'No',
                'Date',
                'Receipt ID',
                'Total',
                'Fuel',
                'Filled',
                'Refund',
                '95',
                '97',
                'B7',
                'B20',
                'Cash',
                'Credit Card',
                'Wallet',
                'Credit Account',
            ]
        ];
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

                $event->sheet->getStyle(static::columnHeader(13))->
                    getFont()->
                    setBold(true)->
                    getColor()->
                    setRGB('ffffff');

                $event->sheet->getDelegate()->
                    getRowDimension('1')->
                    setRowHeight(18);

                $event->sheet->getDelegate()->
                    getStyle(static::columnHeader(13))->
                    getFill()->
                    setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->
                    getStartColor()->
                    setARGB('000000');

                $event->sheet->getStyle(static::columnLetter(13))->
                    getFont()->
                    setBold(true)->
                    getColor()->
                    setRGB('ffffff');
            },
        ];
    }

    public function bindValue(Cell $cell, $value)
    {

        if (is_numeric($value) && strlen($value) > 10) {

            $cell->setValueExplicit($value, DataType::TYPE_STRING);
            return true;
        }

        // else return default behavior
        return parent::bindValue($cell, $value);
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

        return 'A7:' . $letter . '7';
    }

    public static function columnHeader($c)
    {

        $c = intval($c);
        if ($c <= 0) return '';

        $letter = '';

        while ($c != 0) {
            $p = ($c - 1) % 26;
            $c = intval(($c - $p) / 26);
            $letter = chr(65 + $p) . $letter;
        }

        return 'A2:' . $letter . '2';
    }
}
