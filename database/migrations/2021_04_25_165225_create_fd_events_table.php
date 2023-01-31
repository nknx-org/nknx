<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFdEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fd_events', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('fd_deployment_id');
            $table->unsignedBigInteger('user_id');

            $table->string('event_code');

            $table->timestamps();

            $table->foreign('user_id')
            ->references('id')->on('users');

            $table->foreign('fd_deployment_id')
                ->references('id')->on('fd_deployments')
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
        Schema::dropIfExists('fd_events');
    }
}
