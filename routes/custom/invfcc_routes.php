<?php
/*
Invenco ForeCourt Controller (FCC) callback for E1-100
Electronic Payment Server (EPS)
*/

Route::post('/invfcc/get_config',
	'InvencoFccController@get_config')->
	name('invfcc.get_config');

Route::post('/invfcc/get_fuelpoint_state',
	'InvencoFccController@get_fuelpoint_state')->
	name('invfcc.get_fuelpoint_state');

Route::post('/invfcc/reserve_fuelpoint',
	'InvencoFccController@reserve_fuelpoint')->
	name('invfcc.reserve_fuelpoint');

Route::post('/invfcc/free_fuelpoint',
	'InvencoFccController@free_fuelpoint')->
	name('invfcc.free_fuelpoint');

Route::post('/invfcc/authorize_fuelpoint',
	'InvencoFccController@authorize_fuelpoint')->
	name('invfcc.authorize_fuelpoint');

Route::post('/invfcc/terminate_fuelpoint',
	'InvencoFccController@terminate_fuelpoint')->
	name('invfcc.terminate_fuelpoint');

Route::post('/invfcc/lock_fuelsale',
	'InvencoFccController@lock_fuelsale')->
	name('invfcc.lock_fuelsale');

Route::post('/invfcc/clear_fuelsale',
	'InvencoFccController@clear_fuelsale')->
	name('invfcc.clear_fuelsale');

Route::post('/invfcc/get_delivery_details',
	'InvencoFccController@get_delivery_details')->
	name('invfcc.get_delivery_details');

/*
Route::post('/invfcc/fuelpoint_state_change_event',
	'InvencoFccController@fuelpoint_state_change_event')->
	name('invfcc.fuelpoint_state_change_event');

Route::post('/invfcc/fuelprice_change_event',
	'InvencoFccController@fuelprice_change_event')->
	name('invfcc.fuelprice_change_event');

Route::post('/invfcc/delivery_state_change_event',
	'InvencoFccController@delivery_state_change_event')->
	name('invfcc.delivery_state_change_event');

Route::post('/invfcc/delivery_started_event',
	'InvencoFccController@delivery_started_event')->
	name('invfcc.delivery_started_event');

Route::post('/invfcc/delivery_progress_event',
	'InvencoFccController@delivery_progress_event')->
	name('invfcc.delivery_progress_event');

Route::post('/invfcc/delivery_complete_event',
	'InvencoFccController@delivery_complete_event')->
	name('invfcc.delivery_complete_event');
*/
?>
