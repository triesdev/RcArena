<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("user_id");
            $table->unsignedBigInteger("event_id");
            $table->string("user_name");
            $table->string("transaction_number");
            $table->dateTime("transaction_date");
            $table->dateTime("payment_date");
            $table->double("total_price");
            $table->enum("transaction_status",["unpaid","process","success","reject"]);
            $table->timestamps();
            $table->softDeletes();

            // Foreign
            $table->foreign("user_id")->references("id")->on("users");
            $table->foreign("event_id")->references("id")->on("events");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('transactions');
    }
};
