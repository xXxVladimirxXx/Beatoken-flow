<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TransactionHistoryRequest extends FormRequest
{
    public function rules()
    {
        return [
            'description' => 'required|string',
            'amount' =>      'required|numeric',
            'type' =>        'required|string',
            'date' =>        'required|date',
            'hash' =>        'required|string',
            'details' =>     'required|string',
            'user_id' =>     'required|numeric'
        ];
    }
}
