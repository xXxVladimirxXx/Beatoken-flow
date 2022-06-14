<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class Token extends Eloquent
{
    protected $connection = 'mongodb';

    protected $collection = 'tokens';

    protected $fillable = [
        'name',
        'description',
        'attributes',
        'ipfs_hash',
        'flow_id',
        'address'
    ];
}
