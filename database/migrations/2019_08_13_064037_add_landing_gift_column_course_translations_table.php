<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLandingGiftColumnCourseTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('course_translations', function (Blueprint $table) {
            $table->longText('landing_gift')->nullable();
        });

        Schema::table('course_price', function (Blueprint $table) {
            if (Schema::hasColumn('course_price', 'landing_gift')) {
                $table->dropColumn(['landing_gift']);
            }
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('course_translations', function (Blueprint $table) {
           $table->dropColumn(['landing_gift']);
        });
    }
}
