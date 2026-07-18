<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePromotionTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('promotion_translations', function (Blueprint $table) {
            $table->increments('id')->index();
            $table->integer('promotion_id')->unsigned();
            $table->string('locale')->index();
            $table->string('name');
            $table->longText('description')->nullable();
            $table->unique(['promotion_id', 'locale']);
            $table->foreign('promotion_id')->references('id')->on('promotions')->onDelete('cascade');
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
        Schema::dropIfExists('promotion_translations');
    }
}
