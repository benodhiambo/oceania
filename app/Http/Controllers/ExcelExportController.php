<?php

namespace App\Http\Controllers;


use App\Exports\CstoreExport;
use App\Exports\FuelReceiptListExport;
use App\Exports\FuelFullTankReceiptExport;
use App\Exports\StockLedgerExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;


class ExcelExportController extends Controller
{
    //
    public function exportToExcelFuelReceiptlist(Request $request)
    {
        $data = Excel::download(new FuelReceiptListExport($request),
			'fuel_receipt_list.xlsx', \Maatwebsite\Excel\Excel::XLSX);
        ob_end_clean();
        return $data;
    }
    public function exportToExcelFuelFullTankReceiptList(Request $request)
    {
        $data = Excel::download(new FuelFullTankReceiptExport($request),
			'fuel_fulltank_receipt_list.xlsx', \Maatwebsite\Excel\Excel::XLSX);
        ob_end_clean();
        return $data;
    }

    public function exportCstore(Request $request)
    {
        $data = Excel::download(new CstoreExport($request),
			'cstore_receipt_list.xlsx', \Maatwebsite\Excel\Excel::XLSX);
        ob_end_clean();
        return $data;
    }

    public function exportStockLedger()
    {
        $data = Excel::download(new StockLedgerExport(),
            'stock_ledger_excel.xlsx', \Maatwebsite\Excel\Excel::XLSX);
        ob_end_clean();
        return $data;
    }

    public static function cstore_Ntd_generator($arrays, $status, $len)
    {
        $front = '';
        $cnt = 1;
        $keys = array_keys($arrays);

        for ($f = 0; $f <= $len - 1; $f++) {

            if(array_key_exists($f,$arrays)) {


                if ($status == 'voided') {
                    $front .= "<td style='text-align:right;' data-format='0.00'>"
                    . number_format(0, 2) . "</td>\n";
                } else {
                    $front .= "<td style='text-align:right;' data-format='0.00'>"
                    . number_format($arrays[$f] / 100, 2) . "</td>\n";
                }

            }else{
                $front .= '<td> </td>';
            }
        }

        echo $front;
    }

    public static function tdgenerator($position, $value, $len, $status)
    {
        $front = '';
        for ($f = 0; $f <= $position; $f++) {

            if ($f == $position) {
                if ($status == 'voided') {
                    $front .= "<td style='text-align:right;' data-format='0.00'>" .
                    number_format(0, 2) . "</td>\n";
                } else {
                    $front .= "<td style='text-align:right;' data-format='0.00'>" .
                    number_format($value / 100, 2) . "</td>\n";
                }
            } else {
                # code...
                $front .=  "<td> </td>\n";
            }
        }

        for ($i = $position + 1; $i <= $len - 1; $i++) {
            $front .= "  <td> </td>\n";
        }

        echo $front;
    }

    public static function fuel_fulltank_tdgenerator($position, $value, $len)
    {
        $front = '';
        for ($f = 0; $f <= $position; $f++) {

            if ($f == $position) {
                $front .= "<td style='text-align: right;'>"
                . number_format($value / 100, 2) . "</td>\n";
            } else {
                # code...
                $front .=  "  <td> </td>\n";
            }
        }
        for ($i = $position + 1; $i <= $len - 1; $i++) {
            $front .= "  <td> </td>\n";
        }
        echo $front;
    }

    public static function tdgenerator_qty($position, $value, $len, $object)
    {
        $front = '';
        for ($f = 0; $f <= $position; $f++) {

            if ($f == $position) {
                if ($object->status == 'refunded') {
					if ($object->price > 0) {
						$qty = $object->filled/$object->price;
					} else {
						$qty =-0;
					}
                    $front .= "<td style='text-align: right;' data-format='0.00'>"
                    . number_format($qty, 2) . "</td>\n";
                } elseif ($object->status == 'voided') {
                    $front .= "<td style='text-align: right;' data-format='0.00'>"
                    . number_format(0, 2) . "</td>\n";
                } else {
                    $front .= "<td style='text-align: right;' data-format='0.00'>"
                    . number_format($value, 2) . "</td>\n";
                }
            } else {
                # code...
                $front .=  "  <td> </td>\n";
            }
        }
        for ($i = $position + 1; $i <= $len - 1; $i++) {
            $front .= "  <td> </td>\n";
        }
        echo $front;
    }
    public static function getProductNamearray($product_name)
    {
        $newPrdList = [];
        foreach ($product_name as $key => $value) {
            # code...
            array_push($newPrdList, $value->name);
        }
        return $newPrdList;
    }

    public static function FulltankReceiptpaymentMethod($i, $method)
    {
        if ($method > 0 && $i->status == 'refunded') {
            return number_format(($i->total + $i->rounding - $i->refund) / 100, 2);
        } elseif ($i->status == 'voided' && $method > 0) {
            return number_format(0, 2);
        } elseif ($method > 0 && $i->rounding > 0) {
            return number_format(($i->total + $i->rounding) / 100, 2);
        } elseif ($method > 0) {
            return number_format(($i->total) / 100, 2);
        } elseif ($method > 0 && $i->status == 'refunded' &&  $i->rounding > 0) {
            return number_format(($i->total + $i->rounding - $i->refund) / 100, 2);
        }
    }

    public static function FuelReceiptpaymentMethod($i, $method)
    {
        if ($i->status == 'refunded' && $i->refund > 0 && $method > 0) {
            return number_format(($i->filled + $i->newsales_rounding) / 100, 2);
        } elseif ($i->status == 'voided' && $method > 0) {
            return number_format(0, 2);
        } elseif ($i->status != 'voided' && $method > 0 && $i->status != 'refunded') {
            return number_format($i->total / 100, 2);
        } elseif ($method > 0) {
            return number_format($i->total / 100, 2);
        }
    }

    public static function CstorepaymentMethod($i, $method)
    {
        if ($i->status == 'refunded' && $i->refund > 0  && $method > 0) {
            return number_format(($i->total + $i->rounding - $i->refund - $i->change) / 100, 2);
        } elseif ($i->status == 'voided' && $method > 0) {
            return  number_format(0, 2);
        } elseif ($method > 0) {
            return number_format($i->total / 100, 2);
        }
    }
}
