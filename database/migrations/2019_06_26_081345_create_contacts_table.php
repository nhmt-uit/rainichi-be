<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContactsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contacts', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('type')->default(1);
            $table->string('name');
            $table->string('title')->nullable();
            $table->string('phone');
            $table->string('email')->nullable();
            $table->string('address')->nullable();
            $table->string('user_agent')->nullable();
            $table->text('ip_address')->nullable();
            $table->integer('num_of_employee')->nullable();
            $table->integer('course_id')->unsigned()->nullable();
            $table->text('content')->nullable();
            $table->boolean('is_active')->default(0);
            $table->foreign('course_id')->references('id')->on('course')->onDelete('SET NULL');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contacts');
    }
}
