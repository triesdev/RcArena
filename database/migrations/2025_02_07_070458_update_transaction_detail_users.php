<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateTransactionDetailUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("transaction_detail_users", function (Blueprint $table){
            $table->enum("ticket_user_type",["regular","community"])->default("regular");
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
            $table->dropColumn("ticket_user_type");
        });
    }
}
