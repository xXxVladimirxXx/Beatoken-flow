<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNftsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('nfts', function (Blueprint $table) {
            $table->increments('id');
            $table->string('token_uri');

            $table->string('price')->nullable();
            $table->integer('flow_id')->nullable();

            $table->unsignedInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');

            $table->unsignedInteger('pack_id')->nullable();
            $table->foreign('pack_id')->references('id')->on('packs');

            $table->unsignedInteger('flow_account_id');
            $table->foreign('flow_account_id')->references('id')->on('flow_accounts');
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
        Schema::dropIfExists('nfts');
    }
}
