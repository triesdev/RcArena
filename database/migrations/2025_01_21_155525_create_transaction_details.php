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
        Schema::create('transaction_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("user_id");
            $table->unsignedBigInteger("transaction_id");
            $table->unsignedBigInteger("ticket_bundle_id")->nullable();
            $table->unsignedBigInteger("ticket_id");
            $table->string("user_name");
            $table->string("user_code");
            $table->string("ticket_bundle_name");
            $table->integer("qty");
            $table->double("price");
            $table->double("subtotal_price");
            $table->timestamps();
            $table->softDeletes();

            // Foreign
            $table->foreign("user_id")->references("id")->on("users");
            $table->foreign("transaction_id")->references("id")->on("transactions");
            $table->foreign("ticket_bundle_id")->references("id")->on("ticket_bundles");
            $table->foreign("ticket_id")->references("id")->on("tickets");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('transaction_details');
    }
};
