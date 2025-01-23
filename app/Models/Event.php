<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Event extends Model
{
    use SoftDeletes;
    protected $guarded = [];

    public function eventClass()
    {
        return $this->hasMany(EventClass::class,"event_id","id");
    }
}
