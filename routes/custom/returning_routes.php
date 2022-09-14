<?php
	/* Custom routes for Returning module */

Route::get('cstore_returning_list','ReturningController@cstore_returning_list')->name('cstore_returning_list');
Route::get('cstore_returning','ReturningController@cstore_returning')->name('cstore_returning');
Route::get('get_returning_list','ReturningController@displayReturningNoteList')->name('returning_list');

?>
