<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEvreceiptdetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('evreceiptdetails', function (Blueprint $table) {
			$table->id();
			/* FK to evreceipt.id */
			$table->integer('evreceipt_id')->unsigned();

			/* This stores all the calculated values in cents */
			/* total+rounding = (item_amount+SST) + discount */
			$table->integer('total')->unsigned();
			$table->integer('rounding');

			/* These 3 values must add up to total_amount + rounding */
			$table->integer('item_amount')->unsigned();
			$table->integer('sst')->unsigned();
			$table->integer('discount')->unsigned();

			/* Capturing values from UI */
			$table->integer('cash_received')->unsigned();
			$table->integer('wallet')->unsigned();
			$table->integer('creditac')->unsigned();
			$table->integer('change')->unsigned();
			$table->integer('creditcard')->unsigned();

			/* This is to capture the voidance of the receipt. If false,
			* the total_amount+rounding = 0. */
			$table->boolean('void')->default(false);

			$table->softDeletes();
			$table->timestamps();

			// indexea
			$table->index('evreceipt_id');

			$table->engine = "ARIA";
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('evreceiptdetails');
    }
}
