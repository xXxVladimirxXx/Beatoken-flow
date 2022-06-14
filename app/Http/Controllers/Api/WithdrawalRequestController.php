<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\{WithdrawalRequest};
use Auth;

class WithdrawalRequestController extends Controller
{
    public function index()
    {
        return response()->json(WithdrawalRequest::all(), 200);
    }

    public function store(Request $request)
    {
        $data = $request->only(['amount', 'status']);
        $data['user_id'] = Auth::id();
        return response()->json(WithdrawalRequest::create($data), 200);
    }

    public function update(Request $request, WithdrawalRequest $withdrawalRequest)
    {
        return response()->json($withdrawalRequest->update($request->all()), 200);
    }
}
