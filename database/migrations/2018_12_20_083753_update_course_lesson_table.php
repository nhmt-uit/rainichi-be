<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateCourseLessonTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('course_lesson', function (Blueprint $table) {
            $table->integer('sort_order')->after('lesson_id')->default(1);
            $table->boolean('has_trial')->default(false)->after('sort_order');
            $table->boolean('is_active')->default(true)->after('has_trial');
            $table->string('following_lesson')->nullable();
        });
        Schema::table('lesson', function (Blueprint $table) {
            $table->dropColumn('has_trial');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('course_lesson', function (Blueprint $table) {
            $table->dropColumn('sort_order');
            $table->dropColumn('has_trial');
            $table->dropColumn('is_active');
        });
    }
}
