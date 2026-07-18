<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMailSetting extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mail_setting', function (Blueprint $table) {
            $table->increments('id');
            $table->string('driver')->nullable();
            $table->string('email')->nullable();
            $table->string('name')->nullable();
            $table->string('host')->nullable();
            $table->string('port')->nullable();
            $table->string('password')->nullable();
            $table->string('encryption')->nullable();
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
        Schema::dropIfExists('mail_setting');
    }
}
