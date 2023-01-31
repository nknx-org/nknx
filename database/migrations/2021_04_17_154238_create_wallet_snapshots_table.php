<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWalletSnapshotsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wallet_snapshots', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('wallet_id');

            $table->float('balance',16,8);

            $table->timestamps();

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
        Schema::dropIfExists('wallet_snapshots');
    }
}
