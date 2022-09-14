<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOpenitemQtyDistribTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('openitem_qty_distrib', function (Blueprint $table) {
            $table->id();
            // FK to openitem_cost.id
            $table->integer('openitem_cost_id')->unsigned();
            $table->string('doc_no')->nullable();
            $table->integer('qty')->nullable();
            
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
        Schema::dropIfExists('openitem_qty_distrib');
    }
}
