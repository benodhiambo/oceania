<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEvitemdetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('evitemdetails', function (Blueprint $table) {
            $table->id();
            $table->integer('evreceiptproduct_id')->unsigned();

            /* This stores all the calculated values in cents */
            /* amount+rounding = (price+SST) + discount
             */
            $table->integer('amount')->unsigned();
            $table->integer('rounding');

            /* These 3 values must add up to total_amount + rounding */
            $table->integer('price')->unsigned();
            $table->integer('sst')->unsigned();
            $table->integer('discount')->unsigned();

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
        Schema::dropIfExists('evitemdetails');
    }
}
