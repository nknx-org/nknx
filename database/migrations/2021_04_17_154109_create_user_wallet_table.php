<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserWalletTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_wallet', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('wallet_id');

            $table->string('label')->nullable();

            $table->timestamps();

            $table->foreign('user_id')
            ->references('id')->on('users')
            ->onDelete('cascade');

            $table->foreign('wallet_id')
            ->references('id')->on('wallets')
            ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_wallet');
    }
}
