<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WithdrawalRequest extends Model
{
    protected $with = ['user'];

    protected $fillable = [
        'user_id',
        'amount',
        'status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
