<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCvNameColumnUserApplyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_apply', function (Blueprint $table) {
            $sm = Schema::getConnection()->getDoctrineSchemaManager();
            $indexesFound = $sm->listTableIndexes('user_apply');

            if (Schema::hasColumn('user_apply', 'user_id') && array_key_exists("user_apply_article_id_user_id_unique", $indexesFound)) {
                $table->dropForeign(['user_id']);
                $table->dropUnique('user_apply_article_id_user_id_unique');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            }

            if (Schema::hasColumn('user_apply', 'article_id') && array_key_exists("user_apply_article_id_ip_address_unique", $indexesFound)) {
                $table->dropForeign(['article_id']);
                $table->dropUnique('user_apply_article_id_ip_address_unique');
                $table->foreign('article_id')->references('id')->on('articles')->onDelete('cascade');
            }
            $table->string('cv_name')->nullable()->index();
            $table->integer('updated_by')->unsigned()->nullable();
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
        Schema::table('user_apply', function (Blueprint $table) {
            $table->dropColumn(['cv_name', 'updated_by']);
        });
    }
}
