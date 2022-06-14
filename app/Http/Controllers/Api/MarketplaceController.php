<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Services\TransactionHistoryService;
use Illuminate\Support\Facades\Mail;
use App\Mail\TokenBought;
use App\Models\{Nft};
use Auth;

class MarketplaceController extends Controller
{
    public function setPrice(Nft $nft, Request $request) {
        $nft->update($request->only('price'));
        return response()->json($nft, 200);
    }

    public function transferNftToMe(Nft $nft, Request $request, TransactionHistoryService $transactionHistoryService) {
        $oldNft = clone $nft->load('user');

        $nft->update([
            'user_id' => Auth::id(),
            'pack_id' => NULL,
            'flow_account_id' => Auth::user()->flow_account_id,
            'price' => NULL
        ]);
        $nft->drops()->detach();

        $transactionHistoryService->createTransactionHistoryWhenUserBoughtCommission($request);
        $transactionHistoryService->createTransactionHistoryWhenUserBoughtNft($oldNft, $request);

        Mail::to($nft->user->email)
            ->send(new TokenBought($oldNft));

        return response()->json($nft, 200);
    }
}
