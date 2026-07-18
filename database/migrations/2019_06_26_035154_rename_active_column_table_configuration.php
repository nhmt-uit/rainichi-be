<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameActiveColumnTableConfiguration extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('configuration', function (Blueprint $table) {

            $table->renameColumn('active', 'is_active');
            $table->string('app_store_link')->nullable();
            $table->string('play_store_link')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('configuration', function (Blueprint $table) {

            $table->renameColumn('is_active', 'active');
            $table->dropColumn(['app_store_link', 'play_store_link']);
        });
    }
}
