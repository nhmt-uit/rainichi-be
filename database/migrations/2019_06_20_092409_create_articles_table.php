<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateArticlesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->increments('id')->index();
            $table->string('image')->nullable();
            $table->integer('type')->default(1);
            $table->integer('view')->default(0);
            $table->integer('share_facebook')->default(0);
            $table->integer('share_twitter')->default(0);
            $table->integer('share_google')->default(0);
            $table->integer('sort')->default(1);
            $table->date('expired_at')->nullable();
            $table->string('offer_to')->nullable();
            $table->boolean('active')->default(1);
            $table->integer('created_by')->unsigned()->nullable();
            $table->integer('updated_by')->unsigned()->nullable();
            $table->timestamps();
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('news');
    }
}
