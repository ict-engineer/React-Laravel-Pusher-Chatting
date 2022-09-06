<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoiceDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoice_details', function (Blueprint $table) {
            $table->string("inv_dtl_id")->unique();
            $table->string("inv_id");
            $table->string("inv_dtl_is_manual");
            $table->date("inv_dtl_date");
            $table->time("inv_dtl_from");
            $table->time("inv_dtl_to");
            $table->float("inv_dtl_total_hrs");
            $table->float("inv_dtl_hourly_rate");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('invoice_details');
    }
}
