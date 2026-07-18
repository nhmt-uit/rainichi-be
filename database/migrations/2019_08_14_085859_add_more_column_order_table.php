<?php

use Doctrine\DBAL\Types\FloatType;
use Doctrine\DBAL\Types\Type;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMoreColumnOrderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     * @throws \Doctrine\DBAL\DBALException
     */
    public function up()
    {
        if (!Type::hasType('double')) {
            Type::addType('double', FloatType::class);
        }
        Schema::table('order', function (Blueprint $table) {
            if (!Schema::hasColumn('order', 'classroom_id')) {
                $table->integer('classroom_id')->unsigned()->nullable();
                $table->foreign('classroom_id')->references('id')->on('classroom')->onDelete('set null');
            }
            if (!Schema::hasColumn('order', 'course_price_currency_id')) {
                $table->integer('course_price_currency_id')->unsigned()->nullable();
                $table->foreign('course_price_currency_id')->references('id')->on('course_price_currency')->onDelete('set null');
            }
            $table->integer('customer_type_id')->default(2);
            $table->double('discount_credits')->default(0);
            $table->double('buying_credits')->default(0)->change();
            $table->double('reward_credits')->default(0)->change();
            $table->string('payment_method')->nullable();
            $table->mediumText('order_msg')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('order', function (Blueprint $table) {

            if (Schema::hasColumn('order', 'classroom_id')) {
                $table->dropForeign(['classroom_id']);
                $table->dropColumn(['classroom_id']);
            }
            if (Schema::hasColumn('order', 'course_price_currency_id')) {
                $table->dropForeign(['course_price_currency_id']);
                $table->dropColumn(['course_price_currency_id']);
            }
            $table->dropColumn(['customer_type_id', 'discount_credits', 'payment_method', 'order_msg']);
        });
    }
}
