<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MenuRole extends Model
{
    protected $table = 'menu_role';
    protected $guarded = ['id'];
}
