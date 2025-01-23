<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateTransactionDetailNullable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("transaction_details", function (Blueprint $table) {
           $table->unsignedBigInteger("ticket_bundle_id")->nullable()->change();
           $table->string("ticket_bundle_name")->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table("transaction_details", function (Blueprint $table) {
            $table->unsignedBigInteger("ticket_bundle_id")->change();
            $table->string("ticket_bundle_name")->change();
        });
    }
}
