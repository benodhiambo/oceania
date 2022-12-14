<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCreditactTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('creditact', function (Blueprint $table) {
            $table->id();
			// FK to company.id
			$table->integer('company_id')->unsigned();
			// To store the total of the credited amount
			$table->integer('amount')->nullable();
			// Company type
			$table->enum('ctype', ['oneway','twoway'])->default('oneway');
			$table->enum('status', ['active','inactive'])->default('inactive');
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
        Schema::dropIfExists('creditact');
    }
}
