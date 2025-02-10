<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class DenormalizeTransactions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::beginTransaction();

        try {
            Schema::table('transactions', function (Blueprint $table) {
                $table->string('event_name')->nullable();
            });

            Schema::table('transaction_details', function (Blueprint $table) {
                $table->unsignedBigInteger('event_id')->nullable()->after('ticket_id');
                $table->string('event_name')->nullable()->after('event_id');
                $table->unsignedBigInteger('class_id')->nullable()->after('event_id');
                $table->string('class_name')->nullable()->after('event_name');

                $table->foreign('event_id')->references('id')->on('events');
                $table->foreign('class_id')->references('id')->on('classes');
            });

            Schema::table('transaction_detail_users', function (Blueprint $table) {
                $table->unsignedBigInteger('event_id')->nullable()->after('transaction_detail_id');
                $table->string('event_name')->nullable();
                $table->unsignedBigInteger('class_id')->nullable()->after('event_id');
                $table->string('class_name')->nullable();
                $table->unsignedBigInteger('ticket_id')->nullable()->after('class_id');
                $table->string('ticket_name')->nullable();
                $table->string('user_name')->nullable();
                $table->unsignedBigInteger('origin_transfer_user_id')->nullable();

                $table->foreign('event_id')->references('id')->on('events');
                $table->foreign('class_id')->references('id')->on('classes');
                $table->foreign('ticket_id')->references('id')->on('tickets');
            });

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::beginTransaction();

        try {
            Schema::table('transaction_detail_users', function (Blueprint $table) {
                $table->dropForeign(['event_id']);
                $table->dropForeign(['class_id']);
                $table->dropForeign(['ticket_id']);

                $table->dropColumn([
                    'event_id',
                    'event_name',
                    'class_id',
                    'class_name',
                    'ticket_id',
                    'ticket_name',
                    'user_name',
                    'origin_transfer_user_id',
                ]);
            });

            Schema::table('transaction_details', function (Blueprint $table) {
                $table->dropForeign(['event_id']);
                $table->dropForeign(['class_id']);

                $table->dropColumn([
                    'event_id',
                    'event_name',
                    'class_id',
                    'class_name',
                ]);
            });

            Schema::table('transactions', function (Blueprint $table) {
                $table->dropColumn('event_name');
            });

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
