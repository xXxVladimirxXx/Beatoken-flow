<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class NftRequest extends FormRequest
{
    public function rules()
    {
        return [
            'name'           => 'required|string|max:255',
            'file'           => 'required|file',
            'cover_image'    => 'required|file',
            'description'    => 'required|string',
            'type'           => 'string',
            'author'         => 'string',
            'creator'        => 'string',
            'creator_avatar' => 'file',
            'numbering'      => 'string',
            'price'          => 'numeric',
            'pack_id'        => 'numeric',
        ];
    }
}
