<?php

namespace App\Services;

use FlowSDK\Flow;
use Illuminate\Http\Request;
use App\Models\{FlowAccount, User};
use App\Jobs\Flow\MintBLUcouins;

class FlowService
{
    public function createNft($nftData)
    {
        try {
            $flow = new Flow;
            $root_transactions = './transactions/backendTransactions/';

            $result = $flow->transaction($root_transactions . 'CreateNft.cdc')
                ->signer(env('SIGNER_NON_FUNGIBLE_BEATOKEN', 'account-beatoken'))
                ->argAddress(FlowAccount::find($nftData['flow_account_id'])->address)
                ->argString($nftData['name'])
                ->argString($nftData['ipfs_hash'])
                ->argString($nftData['token_uri'])
                ->argString($nftData['description'])
                ->run();

            return $this->getNftId($result);

        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    // This needs to be processed correctly in the future
    public function getNftId($result) {
        return json_decode($result, true)['events'][0]['values']['value']['fields'][0]['value']['value'];
    }

    public function refillBalance(User $user, $request)
    {
        MintBLUcouins::dispatch($user->currentFlowAccount->address, $request->amount)->onQueue('MintBLUcouins');
    }
}
