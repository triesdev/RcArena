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
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("class_id");
            $table->string("class_name")->nullable();
            $table->unsignedBigInteger("event_id");
            $table->string("event_name")->nullable();
            $table->unsignedBigInteger("ticket_bundle_id")->nullable();
            $table->string("name");
            $table->string("ticket_type");
            $table->double("price")->default(0);
            $table->integer("quota_left")->default(0);
            $table->integer("quota")->default(0);
            $table->tinyInteger("is_active")->default(1);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign("class_id")->references("id")->on("classes");
            $table->foreign("event_id")->references("id")->on("events");
            $table->foreign("ticket_bundle_id")->references("id")->on("ticket_bundles");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('tickets');
    }
};
