<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCategoryToCourseType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('course_type', function (Blueprint $table) {
            $table->integer('category')->default(1)->after('id');
        });
        Schema::table('course', function (Blueprint $table) {
            $table->integer('category')->default(1)->after('id');
        });
        Schema::table('chapter', function (Blueprint $table) {
            $table->integer('category')->default(1)->after('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('course_type', function (Blueprint $table) {
            $table->dropColumn('category');
        });
        Schema::table('course', function (Blueprint $table) {
            $table->dropColumn('category');
        });
        Schema::table('chapter', function (Blueprint $table) {
            $table->dropColumn('category');
        });
    }
}
