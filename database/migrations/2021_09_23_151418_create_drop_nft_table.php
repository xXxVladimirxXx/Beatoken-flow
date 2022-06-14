<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDropNftTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('drop_nft', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('drop_id')->nullable();
            $table->foreign('drop_id')->references('id')->on('drops');

            $table->unsignedInteger('nft_id')->nullable();
            $table->foreign('nft_id')->references('id')->on('nfts');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('drop_nft');
    }
}
