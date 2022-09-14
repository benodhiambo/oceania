<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use DB;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;
use PhpOffice\PhpSpreadsheet\Cell\DataType;

class StockLedgerExport extends DefaultValueBinder implements FromView, WithEvents, WithCustomValueBinder, ShouldAutoSize
{
    protected $tableHeaderLength = 6;
    /**
    * @return \Illuminate\Support\view
    */
    public function view(): View
    {
        $locprods = DB::select(DB::raw("
            SELECT
                sr.id,
                sr.systemid as product_systemid,
                sr.type,
                s.updated_at as last_update,
                COUNT(c.cost) as cost,
                s.quantity as qty
            FROM
                locprod_productledger l,
                locationproduct_cost c,
                stockreportproduct s,
                stockreport sr,
                product p
            WHERE
                s.stockreport_id = sr.id  AND
                s.product_id = p.id AND
                l.product_systemid = p.systemid AND
                l.id = c.locprodprodledger_id  AND
                l.product_systemid = p.systemid
            GROUP BY
                c.cost
            HAVING
                COUNT(c.cost) > 1;
            ;
        "));
       $openIten = DB::select(DB::raw("

            SELECT
                sr.id,
                sr.systemid  as product_systemid,
                sr.type,
                s.updated_at as last_update,
                COUNT(c.cost) as cost,
                s.quantity as qty
            FROM
                openitem_productledger o,
                openitem_cost c,
                stockreportproduct s,
                stockreport sr,
                product p
            WHERE
                sr.id = s.stockreport_id  AND
                s.product_id = p.id AND
                o.product_systemid = p.systemid AND
                o.id = c.openitemprodledger_id  AND
                o.product_systemid = p.systemid
            GROUP BY
                c.cost
            HAVING
                COUNT(c.cost) > 1;

            ;
        "));

        $prod_i = DB::select(DB::raw("
            SELECT
                p.name,
                p.systemid
            FROM
                product p,
                prd_inventory pi
            WHERE
                p.id = pi.product_id
            ;")
        );

        $prod_o   = DB::select(DB::raw("
            SELECT
                p.name,
                p.systemid
            FROM
                product p,
                prd_openitem op
            WHERE
                p.id = op.product_id
            ;")
        );

        $products = array_merge($prod_o, $prod_i);
        $products = collect($products)->sortBy('name')->values();
        $locprods = array_merge($locprods, $openIten);
        $stockledger = collect($locprods)->sortBy('product_systemid')->values()->all();
        return view('excel_export.stock_ledger_excel',[
            'stock_ledgers' => $stockledger,
            'products' => $products,
        ]);
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

    /**
     * Write code on Method
     *
     * @return response()
     */
    public function registerEvents(): array
    {

        return [

            AfterSheet::class    => function (AfterSheet $event) {

                $event->sheet->getDelegate()->getRowDimension('1')->setRowHeight(18);

                $event->sheet->getDelegate()->getStyle(static::columnLetter($this->tableHeaderLength))
                    ->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setARGB('000000');

                $event->sheet->getStyle(static::columnLetter(
                    $this->tableHeaderLength
                ))->getFont()->setBold(true)->getColor()->setRGB('ffffff');


            },

        ];
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

    function cellColor($cells,$color){
        global $objPHPExcel;

        $objPHPExcel->getActiveSheet()->getStyle($cells)->getFill()->applyFromArray(array(
            'type' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
            'startcolor' => array(
                'rgb' => $color
            )
        ));

    }



}
