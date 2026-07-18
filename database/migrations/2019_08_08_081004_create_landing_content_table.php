<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLandingContentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('landing_content', function (Blueprint $table) {
            $table->increments('id')->index();
            $table->integer('parent_id')->unsigned()->nullable();
            $table->integer('landing_type_id')->unsigned()->nullable();
            $table->integer('landing_page_id')->unsigned()->nullable();
            $table->string('image')->nullable();
            $table->string('url', 250)->nullable();
            $table->string('video')->nullable();
            $table->integer('sort')->default(1);
            $table->boolean('is_active')->default(1);
            $table->integer('created_by')->unsigned()->nullable();
            $table->integer('updated_by')->unsigned()->nullable();

            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('parent_id')->references('id')->on('landing_content')->onDelete('cascade');
            $table->foreign('landing_type_id')->references('id')->on('landing_type')->onDelete('cascade');
            $table->foreign('landing_page_id')->references('id')->on('landing_page')->onDelete('cascade');
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
        Schema::dropIfExists('landing_content');
    }
}
