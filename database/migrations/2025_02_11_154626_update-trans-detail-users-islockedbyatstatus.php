<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateTransDetailUsersIslockedbyatstatus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("transaction_detail_users", function (Blueprint $table){
            $table->tinyInteger("is_locked")->default(0);
            $table->unsignedBigInteger("is_locked_by")->default();
            $table->dateTime("is_locked_at")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table("transaction_detail_users", function (Blueprint $table){
            $table->dropColumn("is_locked");
            $table->dropColumn("is_locked_by");
            $table->dropColumn("is_locked_at");
        });
    }
}
