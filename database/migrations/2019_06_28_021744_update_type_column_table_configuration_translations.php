<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateTypeColumnTableConfigurationTranslations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('configuration_translations', function (Blueprint $table) {
            $table->text('keywords')->nullable(true)->change();
            $table->longText('description')->nullable(true)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('configuration_translations', function (Blueprint $table) {
            $table->text('keywords')->nullable(true)->change();
            $table->longText('description')->nullable(true)->change();
        });
    }
}
