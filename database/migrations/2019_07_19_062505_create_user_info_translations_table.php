<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserInfoTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_info_translations', function (Blueprint $table) {
            $table->increments('id')->index();
            $table->integer('user_info_id')->unsigned();
            $table->string('locale')->index();
            $table->string('content')->nullable();;
            $table->string('diploma')->nullable();
            $table->string('major')->nullable();
            $table->unique(['user_info_id', 'locale']);
            $table->foreign('user_info_id')->references('id')->on('user_info')->onDelete('cascade');
        });

        if (Schema::hasColumn('user_info', 'diploma'))
        {
            Schema::table('user_info', function (Blueprint $table)
            {
                $table->dropColumn('diploma');
            });
        }

        if (Schema::hasColumn('user_info', 'charge_of_teach'))
        {
            Schema::table('user_info', function (Blueprint $table)
            {
                $table->dropColumn('charge_of_teach');
            });
        }

        if (Schema::hasColumn('user_info', 'short_content'))
        {
            Schema::table('user_info', function (Blueprint $table)
            {
                $table->dropColumn('short_content');
            });
        }

        if (Schema::hasColumn('user_info', 'content'))
        {
            Schema::table('user_info', function (Blueprint $table)
            {
                $table->dropColumn('content');
            });
        }

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_info_translations');
    }
}
