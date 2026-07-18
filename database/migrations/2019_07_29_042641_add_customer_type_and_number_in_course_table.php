<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCustomerTypeAndNumberInCourseTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('course', function (Blueprint $table) {
            $table->integer('num_of_employee')->nullable()->index();
        });

        Schema::table('course_price', function (Blueprint $table) {
            $table->integer('customer_type_id')->default(1)->index();
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
            $table->dropColumn('num_of_employee');
        });

        Schema::table('course_price', function (Blueprint $table) {
            $table->dropColumn('customer_type_id');
        });
    }
}
