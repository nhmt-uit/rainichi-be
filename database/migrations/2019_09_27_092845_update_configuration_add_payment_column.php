<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateConfigurationAddPaymentColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('configuration_translations', function (Blueprint $table) {
            $table->longText('payment_info')->nullable();
            $table->longText('payment_guide')->nullable();
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
            $table->dropColumn('payment_info');
            $table->dropColumn('payment_guide');
        });
    }
}
