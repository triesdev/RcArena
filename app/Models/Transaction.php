<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    use SoftDeletes;
    protected $guarded = [];

    public function transaction_details(){
        return $this->hasMany(TransactionDetail::class, "transaction_id", "id");
    }

    public function event_classes()
    {
        return $this->transaction_details()->leftJoin("tickets","ticket_id","=","tickets.id")->groupBy('tickets.class_id');
    }

    public function scopeLeftJoinUser($query){
        return $query->leftJoin("users", "transactions.user_id", "=", "users.id");
    }

    public function scopeLeftJoinEvents()
    {
        return $this->leftJoin('events', 'transactions.event_id', '=', 'events.id');
    }
}
