<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLocprodcostCsreceipt extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		// Link table for locationproduct_cost and cstore_receipt
        Schema::create('locprodcost_csreceipt', function (Blueprint $table) {
            $table->id();
			// FK to locationproduct_cost.id
			$table->integer('locprodcost_id')->unsigned();
			// FK to cstore_receipt.id
			$table->integer('csreceipt_id')->unsigned();
			$table->integer('qty_taken')->unsigned();
            $table->softDeletes();
            $table->timestamps();
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
        Schema::dropIfExists('locprodcost_csreceipt');
    }

}
