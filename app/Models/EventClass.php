<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventClass extends Model
{
    use SoftDeletes;
    protected $table = "classes";
    protected $guarded = [];

    public function tickets()
    {
        return $this->hasMany(Ticket::class, "class_id", "id");
    }
}
