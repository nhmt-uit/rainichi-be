<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddEnterpriseColumnTestTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('test', function (Blueprint $table) {
            $table->double('discount_price')->default(0);
            $table->double('enterprise_price')->default(0);
            $table->double('enterprise_discount_price')->default(0);
            $table->integer('customer_type_id')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('test', function (Blueprint $table) {
            $table->dropColumn(['discount_price', 'enterprise_price', 'enterprise_discount_price', 'customer_type_id']);
        });
    }
}
