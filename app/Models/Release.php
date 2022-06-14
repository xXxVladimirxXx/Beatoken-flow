<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Release extends Model
{
    protected $with = ['set'];

    protected $fillable = [
        'name',
        'description',
        'set_id'
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    public function set()
    {
        return $this->belongsTo(Set::class);
    }
}
