<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Doctrine\DBAL\Types\FloatType;
use Doctrine\DBAL\Types\Type;

class AddTotalScoreTestResultDetailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Type::hasType('double')) {
            Type::addType('double', FloatType::class);
        }

        Schema::table('test_result_detail', function (Blueprint $table) {
            $table->integer('question')->default(0)->unsigned();
            $table->double('total_score')->default(0)->unsigned();
            $table->double('score')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('test_result_detail', function (Blueprint $table) {
            $table->dropColumn(['question']);
            $table->dropColumn(['total_score']);
        });
    }
}
