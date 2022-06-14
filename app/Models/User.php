<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;

    protected $appends = ['full_uri_avatar'];

    protected $with = ['role', 'flowAccounts', 'currentFlowAccount'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'flow_account_id',
        'avatar_url',
        'role_id',
        'twitter_url',
        'instagram_url',
        'facebook_url'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function flowAccounts()
    {
        return $this->hasMany(FlowAccount::class);
    }

    public function currentFlowAccount()
    {
        return $this->belongsTo(FlowAccount::class, 'flow_account_id');
    }

    public function nfts()
    {
        return $this->hasMany(Nft::class);
    }

    public function drops()
    {
        return $this->hasMany(Drop::class);
    }

    public function getFullUriAvatarAttribute()
    {
        return ($this->avatar_url) ? $this->full_uri_avatar = Storage::disk('public')->url($this->avatar_url) . '?' . date_timestamp_get(date_create()) : url('/') . '/assets/img/default-avatar.svg';
    }
    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
}
