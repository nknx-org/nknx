<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBlockchainTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('blockchain_transactions', function (Blueprint $table) {
            $table->id();

            $table->string('hash')->index();
            $table->string('txType')->index();
            $table->integer('block_height')->unsigned()->index();
            $table->string('senderWallet')->nullable()->index();
            $table->string('recipientWallet')->nullable()->index();
            $table->bigInteger('reward')->nullable();
            $table->string('signerPk')->nullable()->index();

            $table->dateTime('added_at');
            $table->dateTime('created_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('blockchain_transactions');
    }
}
