<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNshiftTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('nshift', function (Blueprint $table) {
            $table->id();
			$table->timestamp('in')->nullable();
			$table->timestamp('out')->nullable();
			// FK to users.id
			$table->integer('staff_user_id')->unsigned()->nullable();
			// FK to fuel_receipt.id
			$table->integer('fuel_receipt_id')->unsigned()->nullable();
			$table->integer('cash')->unsigned()->nullable();
			$table->integer('cash_in')->unsigned()->nullable();
			$table->integer('cash_out')->unsigned()->nullable();
			$table->integer('sales_drop')->unsigned()->nullable();
			$table->integer('actual')->unsigned()->nullable();
			$table->integer('difference')->unsigned()->nullable();
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
        Schema::dropIfExists('nshift');
    }
}
