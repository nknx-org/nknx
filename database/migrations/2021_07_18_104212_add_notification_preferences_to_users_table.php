<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNotificationPreferencesToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->json('notificationPreferences')->default('{"node_offline":["mail", "database"],"node_online_again":["mail", "database"],"needs_id_generation":["database"],"node_generated_id":["database"],"node_mined_block":["database"],"receive_nkn":[],"send_nkn":[],"node_installation_started":[]}');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('notificationPreferences');
        });
    }
}
