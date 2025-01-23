<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateTransactionsDropEventIf extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("transactions", function (Blueprint $table){
            // DROP CONTRAINT
            $table->dropForeign(["event_id"]);
            $table->dropColumn("event_id");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table("transactions", function (Blueprint $table){
            $table->unsignedBigInteger("event_id")->after("id");
        });
    }
}
