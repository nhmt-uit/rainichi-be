<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAndRemoveColumnCourseTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('course', function (Blueprint $table) {
            if (Schema::hasColumn('course', 'landing_gift')) {
                $table->dropColumn(['landing_gift']);
            }
            if (Schema::hasColumn('course', 'landing_buy_credit')) {
                $table->dropColumn(['landing_buy_credit']);
            }
            if (Schema::hasColumn('course', 'landing_discount_credit')) {
                $table->dropColumn(['landing_discount_credit']);
            }
        });

        Schema::table('course_price', function (Blueprint $table) {
            $table->string('discount_credits')->nullable();
            $table->string('landing_gift')->nullable();
            $table->string('landing_buy_credit')->nullable();
            $table->string('landing_discount_credit')->nullable();
            $table->string('landing_reward_credit')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('course_price', function (Blueprint $table) {
            $table->dropColumn(['landing_gift', 'landing_buy_credit', 'landing_discount_credit',
                'landing_reward_credit', 'discount_credits']);
        });
    }
}
