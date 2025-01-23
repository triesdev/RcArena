<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateTransactionUniquePayment extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transactions', function (Blueprint $table) {
           $table->dropColumn('payment_date');
           $table->double("unique_code_price")->default(0)->after("transaction_date");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transactions', function (Blueprint $table) {
           $table->dateTime("payment_date")->after("transaction_date");
           $table->dropColumn('unique_code_price');
        });
    }
}
