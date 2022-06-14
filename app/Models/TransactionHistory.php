<?php

namespace App\Models;

use App\Http\Requests\TransactionHistoryRequest;
use Illuminate\Database\Eloquent\Model;

class TransactionHistory extends Model
{
    protected $table = 'transactions_history';

    protected $fillable = [
        'description',
        'amount',
        'type',
        'date',
        'hash',
        'details',
        'user_id'
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];
}
