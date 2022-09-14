<?php
// Custom Excel Export Routes
Route::post(
    'download_fuel_receipt_list',
    'ExcelExportController@exportToExcelFuelReceiptList'
)->name('export_excel_fuel_receipt');

Route::post(
    'download_fuel_fulltank_receiptList',
    'ExcelExportController@exportToExcelFuelFulltankReceiptList'
)->name('export_excel_fuel_fulltank_receiptlist');

Route::post('download_Cstore_excel', 'ExcelExportController@exportCstore')->name('export_cstore_excel');
Route::get('download_stock_ledger_excel', 'ExcelExportController@exportStockLedger')->name('export_stock_ledger_excel');
