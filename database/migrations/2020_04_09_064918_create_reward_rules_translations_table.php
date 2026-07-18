<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRewardRulesTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reward_rules_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('reward_rule_id')->unsigned();
            $table->string('locale')->index();
            $table->string('description')->nullable();
            $table->string('method')->nullable();
            $table->foreign('reward_rule_id')->references('id')->on('reward_rules')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reward_rules_translations');
    }
}
