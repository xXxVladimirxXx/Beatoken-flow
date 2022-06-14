<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReleaseRequest extends FormRequest
{
    public function rules()
    {
        return [
            'name' =>   'required|string|max:255',
            'set_id' => 'required|numeric',
        ];
    }
}
