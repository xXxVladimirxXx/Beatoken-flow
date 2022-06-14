<?php

namespace App\Models;

use App\Traits\Eloquent\Uploadable;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Auth;
use Illuminate\Support\Facades\Storage;
use phpDocumentor\Reflection\Types\Integer;

class Drop extends Model
{
    protected $appends = ['full_uri'];

    protected $with = ['nfts', 'user'];

    protected $fillable = [
        'name',
        'user_id',
        'release_name',
        'release_start',
        'release_end',
        'price',
        'cover_image',
        'short_description',
        'full_description',
        'video_url',
        'number_nfts',
        'status'
    ];

    protected $hidden = ['created_at', 'updated_at'];

    public function createDrop(Request $request) {
        $cover_url = Uploadable::uploadPhoto($request->cover_image,
            $request->file('cover_image')->getClientOriginalName(),
            Auth::id() . '_user_drops'
        );

        $dropData = $request->only(
            'name', 'release_name', 'price', 'short_description',
            'full_description', 'video_url', 'number_nfts', 'status'
        );
        $dropData['user_id'] = Auth::id();
        $dropData['cover_image'] = $cover_url;

        if ((int) $request->utc_user < 0) {
            $dropData['release_start'] = Carbon::parse($request->release_start)->addMinute($request->utc_user);
            $dropData['release_end'] = Carbon::parse($request->release_end)->addMinute($request->utc_user);
        } else {
            $dropData['release_start'] = Carbon::parse($request->release_start)->subMinute(abs($request->utc_user));
            $dropData['release_end'] = Carbon::parse($request->release_end)->subMinute(abs($request->utc_user));
        }

        $drop = $this->create($dropData);

        $drop->nfts()->attach(json_decode($request->idNftsForDrop));
        Nft::whereIn('id', json_decode($request->idNftsForDrop))->update(['price' => $request->priceForNftInFlow]);

        return $drop;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function dropLines()
    {
        return $this->hasMany(DropLine::class);
    }

    public function nfts()
    {
        return $this->belongsToMany(Nft::class, 'drop_nft');
    }

    public function getFullUriAttribute()
    {
        return $this->full_uri = Storage::disk('public')->url($this->cover_image);
    }
}
