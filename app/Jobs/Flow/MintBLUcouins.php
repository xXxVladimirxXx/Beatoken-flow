<?php

namespace App\Jobs\Flow;

use App\Jobs\Job;
use App\Models\Setting;
use FlowSDK\Flow;
use Illuminate\Support\Facades\Cache;

class MintBLUcouins extends Job
{
    public $tries = 10;
    public $backoff = 20;

    protected $keysNumber = 20;
    protected $addressRecipient;
    protected $amount;

    protected $flow;
    protected $signer;
    protected $root_transactions = './transactions/backendTransactions/';

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($addressRecipient, $amount)
    {
        if ($keysNumber = Setting::where('key', 'keys_number')->pluck('value')->first()) $this->keysNumber = $keysNumber;

        $this->addressRecipient = $addressRecipient;
        $this->amount = $amount;

        $this->flow = new Flow;
        $this->signer = env('SIGNER_FUNGIBLE_BEATOKEN', 'account-beatoken');
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        for ($keyIndex = 1; $keyIndex <= $this->keysNumber; $keyIndex++) {
            if (!Cache::has('MintBLUcouins'.$keyIndex)) Cache::put('MintBLUcouins'.$keyIndex, true);

            $lock = Cache::lock('MintBLUcouins'.$keyIndex, 20);

            if ($lock->get()) {
                $response = $this->flow->transaction($this->root_transactions . 'FungibleMintTokens.cdc')
                    ->signer("$this->signer-$keyIndex")
                    ->argAddress($this->addressRecipient)
                    ->argFix($this->amount)
                    ->run();

                $responseArray = json_decode($response, true);
                if (isset($responseArray['error'])) {
                    $this->release(20);
                }

                $lock->release();
                return;
            }
        }
        $this->release(20); // If all keys are busy, wait 20 seconds, try again
    }
}
