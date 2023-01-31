<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('nodes', function (Blueprint $table) {
            $table->id();

            $table->string('syncState')->nullable();
            $table->integer('jsonRpcPort')->nullable();
            $table->string('addr')->unique()->index();
            $table->BigInteger('height')->nullable();
            $table->string('nodeId')->nullable();
            $table->string('publicKey')->nullable()->index();
            $table->integer('websocketPort')->nullable();
            $table->integer('relayMessageCount')->nullable();
            $table->integer('sversion')->nullable();
            $table->string('version')->nullable();
            $table->string('walletAddress')->nullable();
            $table->integer('blocksMined')->default(0);

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
        Schema::dropIfExists('nodes');
    }
}
