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
        Schema::create('transaction_detail_users', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("user_id");
            $table->unsignedBigInteger("transaction_id");
            $table->unsignedBigInteger("transaction_detail_id");
            $table->integer("qty");
            $table->boolean("is_transfered")->default(0);
            $table->boolean("is_verified")->default(0);
            $table->dateTime("verified_at")->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Foreign
            $table->foreign("user_id")->references("id")->on("users");
            $table->foreign("transaction_id")->references("id")->on("transactions");
            $table->foreign("transaction_detail_id")->references("id")->on("transaction_details");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('transaction_detail_users');
    }
};
