<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Pack extends Model
{
    protected $with = ['release'];

    protected $appends = ['full_uri'];

    protected $fillable = [
        'name',
        'uri_file',
        'description',
        'price',
        'release_id'
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    public function release()
    {
        return $this->belongsTo(Release::class);
    }

    public function getFullUriAttribute()
    {
        return $this->full_uri = Storage::disk('public')->url($this->uri_file);
    }
}
