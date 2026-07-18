<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateTableConvertMoneyToCredit extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('convert_money_to_credit', function (Blueprint $table) {
            $table->string('package_id')->default("");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('convert_money_to_credit', function (Blueprint $table) {
            //
            $table->dropColumn(['package_id']);
        });
    }
}
