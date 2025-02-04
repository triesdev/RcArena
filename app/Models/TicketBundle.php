<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TicketBundle extends Model
{
    use HasFactory, SoftDeletes;
    protected $guarded = [];

    public function tickets()
    {
        return $this->hasMany(Ticket::class, "ticket_bundle_id", "id");
    }
}
