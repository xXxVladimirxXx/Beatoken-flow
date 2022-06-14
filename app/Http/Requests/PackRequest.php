<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PackRequest extends FormRequest
{
    public function rules()
    {
        return [
            'name' =>       'required|string|max:255',
            'release_id' => 'required|numeric',
            'file' =>       'required|file',
        ];
    }
}
