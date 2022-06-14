<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'session_id',
        'user_id',
        'amount',
        'status'
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];
}
