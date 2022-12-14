<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOnewayTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       Schema::create('oneway', function (Blueprint $table) {
            $table->id();
            // FK to merchant.id, this is the "owner" of these oneway parties
            $table->integer('self_merchant_id');
            $table->string('systemid');
            $table->string('company_name');
            $table->string('business_reg_no')->nullable();
            $table->integer('credit_limit')->unsigned()->nullable();
            $table->string('address')->nullable();
            $table->string('contact_name')->nullable();
            $table->string('mobile_no')->nullable();
            $table->enum('status', ['pending','active','inactive'])->default('pending');
            $table->softDeletes();
            $table->timestamps();
            $table->engine="ARIA";
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('oneway');
    }
}
