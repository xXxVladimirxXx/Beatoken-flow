<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
{
    public function rules()
    {
        return [
            'name' =>       'required|string|max:21',
            // 'email' =>      'email|max:255',
            'flow_email' => 'string|max:255',
            'flow_addr' =>  'string|max:255',
            'role_id' =>    'numeric',
        ];
    }
}
