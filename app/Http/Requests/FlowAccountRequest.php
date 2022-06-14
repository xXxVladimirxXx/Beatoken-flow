<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FlowAccountRequest extends FormRequest
{
    public function rules()
    {
        return [
            'address' => 'required|string|max:18',
            'email' =>   'required|email'
        ];
    }
}
