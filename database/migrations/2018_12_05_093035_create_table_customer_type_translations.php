<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableCustomerTypeTranslations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_type_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('customer_type_id')->unsigned();
            $table->string('locale');
            $table->string('name');
            $table->unique(['customer_type_id','locale']);
            $table->foreign('customer_type_id')->references('id')->on('customer_type')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('customer_type_translations');
    }
}
