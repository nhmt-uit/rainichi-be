<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnUserNameColumnUserApplyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_apply', function (Blueprint $table) {
            $table->string('name')->nullable()->index();
            $table->string('email')->nullable()->index();
            $table->longText('info')->nullable();
            $table->string('cv')->nullable();
            $table->string('ip_address')->nullable()->index();
            $table->unique(['article_id','ip_address']);
            $table->string('user_agent')->nullable()->index();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $indexNames = collect(Schema::getIndexes('user_apply'))->pluck('name');

        Schema::table('user_apply', function (Blueprint $table) use ($indexNames) {
            if ($indexNames->contains('user_apply_article_id_ip_address_unique')) {
                $table->dropUnique('user_apply_article_id_ip_address_unique');
            }

            $table->dropColumn(['name', 'email', 'info', 'cv', 'ip_address', 'deleted_at', 'user_agent']);
        });
    }
}
