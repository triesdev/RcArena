<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('confirmation_by_user_id')->nullable();
            $table->unsignedBigInteger('transaction_id');
            $table->string("name");
            $table->dateTime("payment_date");
            $table->string("phone_number");
            $table->string("account_name");
            $table->double("nominal_payment");
            $table->string("payment_proof_image_uri");
            $table->boolean("is_confirmed");
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('confirmation_by_user_id')->references('id')->on('users');
            $table->foreign('transaction_id')->references('id')->on('transactions');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payments');
    }
}
