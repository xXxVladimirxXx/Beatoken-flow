<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\{Http, Cache};
use Auth;

class Nft extends Model
{
    protected $appends = ['metadata', 'extension_file'];

    protected $with = ['flowAccount'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'token_uri',
        'price',
        'flow_id',
        'user_id',
        'flow_account_id',
        'pack_id'
    ];

    protected $hidden = ['pack_id', 'flow_account_id', 'updated_at', 'created_at'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function flowAccount()
    {
        return $this->belongsTo(FlowAccount::class);
    }

    public function pack()
    {
        return $this->belongsTo(Pack::class);
    }

    public function drops()
    {
        return $this->belongsToMany(Drop::class, 'drop_nft');
    }

    public function getMetadataAttribute()
    {
        if ($this->token_uri && isset(parse_url($this->token_uri)['host']) && isset(parse_url($this->token_uri)['path'])) {
            $token_uri = $this->token_uri;
            return Cache::remember($this->token_uri, now()->addDays(1), function () use ($token_uri) {
                return json_decode(Http::get($token_uri)->body());
            });
        }
    }

    public function getExtensionFileAttribute()
    {
        if (!isset($this->metadata->source_file)) return null;
        return pathinfo($this->metadata->source_file)['extension'];
    }
}
