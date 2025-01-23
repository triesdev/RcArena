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
        Schema::create("events", function ($q){
            $q->id();
            $q->unsignedBigInteger("user_organizer_id")->nullable();
            $q->unsignedBigInteger("created_by_user_id")->nullable();
            $q->string("name");
            $q->text("image_uri");
            $q->text("description");
            $q->datetime("event_launch_at");
            $q->date("ticket_purchasing_at");
            $q->string("location_name");
            $q->string("location_address");
            $q->date("event_date");
            $q->dateTime("event_start");
            $q->dateTime("event_end");
            $q->text("schedules");
            $q->boolean("is_active")->default(1);
            $q->timestamps();
            $q->softDeletes();

            $q->foreign("user_organizer_id")->references("id")->on("users");
            $q->foreign("created_by_user_id")->references("id")->on("users");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists("events");
    }
};
