<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMerchantrelationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       Schema::create('merchantrelation', function (Blueprint $table) {
            $table->id();
            /* This is for self, FK to merchant.id */
            $table->integer('self_merchant_id')->unsigned();

            /* This is for self, FK to twoway.id */
            $table->integer('twoway_id')->unsigned();

            /* This is for registered/twoway partner, FK to merchant.id */
            $table->integer('partner_merchant_id')->unsigned();

            /* This is for unregistered/oneway partner, FK to oneway.id */
            $table->integer('partner_oneway_id')->unsigned();

            /* Sometimes a partner can be both dealer and supplier, so these
             * 2 columns will be true in that case */
            $table->boolean('is_dealer')->default(false);
            $table->boolean('is_supplier')->default(false);

            /* Default location assigned to partner, FK to location.id */
            $table->integer('default_location_id')->unsigned();

            $table->enum('dealer_status', ['pending','approved','active',
                'rejected','deactivated'])->default('pending');

            $table->enum('supplier_status', ['pending','approved','active',
                'rejected','deactivated'])->default('pending');

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
        Schema::dropIfExists('merchantrelation');
    }
}
