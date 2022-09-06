<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoiceDetailLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoice_detail_logs', function (Blueprint $table) {
            $table->string("inv_dtl_log_id")->unique();
            $table->string("inv_dtl_id");
            $table->string("inv_dtl_log_field_name");
            $table->string("inv_dtl_log_new_value");
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
        Schema::dropIfExists('invoice_detail_logs');
    }
}
