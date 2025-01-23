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
        Schema::create('ticket_bundles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("event_id");
            $table->string("name");
            $table->double('price');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign("event_id")->references("id")->on("events");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('ticket_bundles');
    }
};
