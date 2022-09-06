<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePortfoliosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('portfolios', function (Blueprint $table) {
            $table->string("por_id")->unique();
            $table->string("fre_id");
            $table->string("por_title");
            $table->text("por_desc")->nullable();
            $table->boolean("por_platform_verified")->default(false);
            $table->boolean("por_done_inside_platform")->default(false);
            $table->integer("por_helped")->default(0);
            $table->integer("por_viewed")->default(0);
            $table->string("por_status")->nullable();
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
        Schema::dropIfExists('portfolios');
    }
}
