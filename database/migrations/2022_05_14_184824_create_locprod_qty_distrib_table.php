<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLocprodQtyDistribTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('locprod_qty_distrib', function (Blueprint $table) {
        
            $table->id();
            
            // FK to locationproduct_cost.id
            $table->integer('locprod_cost_id')->unsigned();
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
        Schema::dropIfExists('locprod_qty_distrib');
    }
}
