<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserApplyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_apply', function (Blueprint $table) {
            $table->increments('id')->index();
            $table->integer('user_id')->unsigned()->nullable();
            $table->integer('article_id')->unsigned()->nullable();
            $table->boolean('is_view')->default(0);
            $table->boolean('active')->default(1);
            $table->unique(['article_id', 'user_id']);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('article_id')->references('id')->on('articles')->onDelete('cascade');
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
        Schema::dropIfExists('user_apply');
    }
}
