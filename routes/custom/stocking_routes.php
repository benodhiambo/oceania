<?php

Route::get('stocking/show-product-ledger-sale/{product_id}',
	'CentralStockMgmtController@showproductledger')->
	name('stocking.showproductledger');

Route::get('stocking/show-stock-report/{report_id}',
	'CentralStockMgmtController@showStockReport')->
	name('stocking.stock_report');
	
Route::post('stocking/show-stock-report/qty-cost-table',
	'CentralStockMgmtController@get_qty_cost_datatatble')->
	name('stocking.get_qty_cost_datatatble');
?>
