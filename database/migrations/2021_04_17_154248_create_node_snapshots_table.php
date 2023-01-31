<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNodeSnapshotsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('node_snapshots', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('node_id')->index();

            $table->bigInteger('mined');

            $table->timestamps();

            $table->foreign('node_id')
            ->references('id')->on('nodes')
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
        Schema::dropIfExists('node_snapshots');
    }
}
