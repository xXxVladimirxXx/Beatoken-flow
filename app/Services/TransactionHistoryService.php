<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\{Role, TransactionHistory, User};
use Auth;

class TransactionHistoryService {

    protected $transactionHistory;

    public function __construct(TransactionHistory $transactionHistory) {
        $this->transactionHistory = $transactionHistory;
    }

    public function createTransactionHistoryWhenUserBoughtNft($nft, $request) {

        //coin
        $coinTransaction = [
            'description' => 'Utility coin transaction',
            'amount' => $request->price,
            'type' => 'In',
            'date' => Carbon::now(),
            'hash' => $request->hash,
            'details' => 'Blucoin',
            'user_id' => $nft->user_id,
        ];
        $this->transactionHistory->create($coinTransaction);

        $coinTransaction['type'] = 'Out';
        $coinTransaction['user_id'] = Auth::id();
        $this->transactionHistory->create($coinTransaction);

        //NFT
        $nftTransaction = [
            'description' => 'NFT transaction',
            'amount' => 1,
            'type' => 'Out',
            'date' => Carbon::now(),
            'hash' => $request->hash,
            'details' => $nft->metadata->name,
            'user_id' => $nft->user_id,
        ];
        $this->transactionHistory->create($nftTransaction);

        $nftTransaction['type'] = 'In';
        $nftTransaction['user_id'] = Auth::id();
        $this->transactionHistory->create($nftTransaction);
    }

    public function createTransactionHistoryWhenUserBoughtCommission($request) {

        $middleman_id = Role::where('name', 'middleman')->pluck('id')->first();
        $feeTransaction = [
            'description' => 'Fee transaction',
            'amount' => $request->commission,
            'type' => 'In',
            'date' => Carbon::now(),
            'hash' => $request->hash,
            'details' => 'Blucoin',
            'user_id' => User::where('role_id', $middleman_id)->pluck('id')->first()
        ];

        $this->transactionHistory->create($feeTransaction);

        $feeTransaction['type'] = 'Out';
        $feeTransaction['user_id'] = Auth::id();
        $this->transactionHistory->create($feeTransaction);
    }

    public function createTransactionHistoryWhenUserRefillBalance($request) {

        $refillBalanceTransaction = [
            'description' => 'Refill balance with Stripe',
            'amount' => $request['amount'],
            'type' => 'In',
            'date' => Carbon::now(),
            'hash' => $request['session_id'],
            'details' => 'Blucoin',
            'user_id' => Auth::id()
        ];

        $this->transactionHistory->create($refillBalanceTransaction);
    }
}
