<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNodeUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('node_user', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('node_id');

            $table->string('hostname')->nullable();
            $table->string('label')->nullable();
            $table->timestamp('notified_offline')->nullable();
            $table->timestamp('notified_outdated')->nullable();
            $table->timestamp('notified_stuck')->nullable();
            $table->boolean('validHostname')->nullable();
            $table->timestamps();

            $table->foreign('user_id')
            ->references('id')->on('users')
            ->onDelete('cascade');

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
        Schema::dropIfExists('node_user');
    }
}
