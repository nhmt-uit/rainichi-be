<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnToCourseClassTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('course_class', function (Blueprint $table) {

            $table->integer('type')->default(1);
            $table->integer('num_of_employee')->nullable();
            $table->integer('duration')->nullable();
            $table->double('buying_credits')->nullable();
            $table->double('reward_credits')->nullable();
            $table->dateTime('active_date')->nullable();
            $table->dateTime('expired_date')->nullable();
            $table->boolean('is_expired')->default(0);
            $table->boolean('is_approved')->default(0);
            $table->integer('active_by')->unsigned()->nullable();
            $table->integer('approved_by')->unsigned()->nullable();
            $table->integer('created_by')->unsigned()->nullable();
            $table->integer('updated_by')->unsigned()->nullable();

            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('active_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');

            $table->boolean('is_active')->default(0)->change();
        });


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('course_class', function (Blueprint $table) {
            $table->dropForeign(['active_by']);
            $table->dropForeign(['approved_by']);
            $table->dropForeign(['created_by']);
            $table->dropForeign(['updated_by']);
            $table->dropColumn([
                'duration', 'buying_credits', 'reward_credits', 'created_by', 'updated_by', 'type', 'num_of_employee',
                'active_date', 'expired_date', 'is_expired', 'is_approved', 'active_by', 'approved_by'
            ]);
        });
    }
}
