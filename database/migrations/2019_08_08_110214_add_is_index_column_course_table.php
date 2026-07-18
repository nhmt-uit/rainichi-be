<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIsIndexColumnCourseTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('course', function (Blueprint $table) {
            $table->string('landing_gift')->nullable();
            $table->string('landing_buy_credit')->nullable();
            $table->string('landing_discount_credit')->nullable();
            $table->boolean('is_index')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('course', function (Blueprint $table) {
            $table->dropColumn(['is_index', 'landing_gift', 'landing_buy_credit', 'landing_discount_credit']);
        });
    }
}
