<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNewSyncModesToFdConfigurationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('fd_configurations', function (Blueprint $table) {
            $table->boolean('fast_sync')->default(false);
            $table->boolean('light_sync')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('fd_configurations', function (Blueprint $table) {
            $table->dropColumn('fast_sync');
            $table->dropColumn('light_sync');
        });
    }
}
