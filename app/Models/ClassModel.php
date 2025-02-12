<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassModel extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $table = 'classes';

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function ticket()
    {
        return $this->hasMany(Ticket::class, 'class_id', 'id')->where('ticket_bundle_id', null);
    }
}
