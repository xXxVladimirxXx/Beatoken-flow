<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FlowAccount extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'address',
        'email',
        'user_id',
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function nfts()
    {
        return $this->hasMany(Nft::class);
    }
}
