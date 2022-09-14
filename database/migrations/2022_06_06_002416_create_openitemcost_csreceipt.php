<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOpenitemcostCsreceipt extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('openitemcost_csreceipt', function (Blueprint $table) {
            $table->id();
			// FK to openitem_cost.id
			$table->integer('openitemcost_id')->unsigned();
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
        Schema::dropIfExists('openitemcost_csreceipt');
    }

}
