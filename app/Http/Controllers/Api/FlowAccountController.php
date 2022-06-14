<?php

namespace App\Http\Controllers\Api;

use App\Models\{User, FlowAccount};
use App\Http\Requests\FlowAccountRequest;
use Auth;

class FlowAccountController extends Controller
{
    public function index() {
        return response()->json(FlowAccount::with(['user', 'nfts'])->get(), 200);
    }

    public function store(FlowAccountRequest $request) {

        $alreadyExistingFlowAccount = FlowAccount::where('address', $request->address)->first();
        if ($alreadyExistingFlowAccount) {
            $user = User::find(Auth::id());
            $user->flow_account_id = $alreadyExistingFlowAccount->id;
            $user->save();

            return response()->json($alreadyExistingFlowAccount, 200);
        }

        $flowAccountData = $request->only('address', 'email');
        $flowAccountData['user_id'] = Auth::id();
        $flowAccount = FlowAccount::create($flowAccountData);

        $user = User::find($flowAccount->user_id);
        $user->flow_account_id = $flowAccount->id;
        $user->save();

        return response()->json($flowAccount, 200);
    }

    public function show(FlowAccount $flowAccount) {
        return response()->json($flowAccount, 200);
    }

    public function getByAddress($address) {
        return response()->json(FlowAccount::where('address', $address)->first(), 200);
    }
}
