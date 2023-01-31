<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFdDeploymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fd_deployments', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('fd_configuration_id');

            $table->string('provider');
            $table->string('label')->nullable();
            $table->string('ip')->nullable();
            $table->string('secret');
            $table->string('architecture');

            $table->timestamps();

            $table->foreign('fd_configuration_id')
                ->references('id')->on('fd_configurations')
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
        Schema::dropIfExists('fd_deployments');
    }
}
