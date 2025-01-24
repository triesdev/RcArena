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

    public function transaction_detail_class_groups()
    {
        return $this->transaction_details()->leftJoin("tickets","ticket_id","=","tickets.id")->leftJoin("classes","classes.id","=","tickets.class_id")->select("classes.name","classes.id as class_id","tickets.id as ticket_id","transaction_details.id","transaction_details.transaction_id")->groupBy('tickets.class_id');
    }

    public function transaction_detail_classes()
    {
        return $this->transaction_details()->leftJoin("tickets","ticket_id","=","tickets.id")->leftJoin("classes","classes.id","=","tickets.class_id")->select("classes.name as class_name","classes.id as class_id","tickets.id as ticket_id","transaction_details.id","transaction_details.transaction_id")->groupBy('tickets.class_id');
    }

    public function scopeLeftJoinUser($query){
        return $query->leftJoin("users", "transactions.user_id", "=", "users.id");
    }

    public function scopeLeftJoinEvents()
    {
        return $this->leftJoin('events', 'transactions.event_id', '=', 'events.id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, "user_id");
    }
}
