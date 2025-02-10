<?php

namespace App\Http\Repository;

use App\Models\TransactionDetailUser;

class TicketUserRepository
{

    protected $auth_user;
    public function __construct($user)
    {
        $this->auth_user = $user;
    }

    public function getDetailTicketUser($transaction_detail_user_id){
        $user_id = $this->auth_user->id;
        $data = TransactionDetailUser::where("user_id",$user_id); // Untuk mencari transaction detail user by user id

        // untuk memastikan bahwa transaksi yang sudah ditransfer tidak bisa diakses
        $data = $data->where("is_transfered",0);

        // Join ke event untuk mengambil beberapa data
        $data = $data->leftJoin("events","transaction_detail_users.event_id","=","events.id");

        // Select data yang dibutuhkan saja
        $data = $data->select(
            "transaction_detail_users.id as transaction_detail_users_id", // Agar tidak ambigu dengan id table tickets
            "events.name as event_name",
            "events.event_date",
            "events.event_start as event_start_date",
            "events.event_end as event_end_date",
            "events.location_name as event_location_name",
            "events.location_address as event_location_address",
            "transaction_detail_users.class_name", // Tidak perlu join agar lebih cepat
            "transaction_detail_users.ticket_name", // Tidak perlu join agar lebih cepat
            "transaction_detail_users.ticket_user_type", // Tipe nya ini regular dan community
            "transaction_detail_users.ticket_number" // Ini dari generate an otomatis
        );


        // Untuk mencari ticket user by transaction detail user id
        $data = $data->find($transaction_detail_user_id);

        // Mengembalikan data yang sudah diambil
        return $data;
    }
}
