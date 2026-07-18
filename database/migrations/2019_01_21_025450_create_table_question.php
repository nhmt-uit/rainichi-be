<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableQuestion extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('question', function (Blueprint $table) {
            $table->increments('id');
            $table->longText('question')->nullable();
            $table->longText('paragraph')->nullable();
            $table->string('image')->nullable();
            $table->string('media')->nullable();
            $table->longText('media_description')->nullable();
            $table->integer('level_id')->unsigned()->nullable();
            $table->foreign('level_id')->references('id')->on('level')->onDelete('cascade');
            $table->integer('chapter_id')->unsigned()->nullable();
            $table->foreign('chapter_id')->references('id')->on('chapter')->onDelete('cascade');
            $table->boolean('is_active')->default(true);
            $table->boolean('hidden_in_list')->nullable(false);
            $table->integer('type')->nullable();
            $table->boolean('is_skill')->default(false);
            $table->integer('category')->nullable();
            $table->integer('created_by')->unsigned()->nullable();
            $table->integer('updated_by')->unsigned()->nullable();
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('cascade');
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
        Schema::dropIfExists('question');
    }
}
