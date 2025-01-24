<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $guarded = [];

    public function scopeLeftJoinClass()
    {
       return $this->leftJoin('classes', 'tickets.class_id', '=', 'classes.id');
    }

    public function event_class()
    {
        return $this->belongsTo(EventClass::class, "class_id");
    }
}
