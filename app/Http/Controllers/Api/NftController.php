<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\NftRequest;
use Illuminate\Http\Request;
use App\Services\{NftService};
use App\Models\{FlowAccount, Nft, Author};
use Auth;

class NftController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:author')->only('store');
    }

    public function index() {
        return response()->json(Nft::where('user_id', Auth::id())->with('drops')->get(), 200);
    }

    public function allNftsOfAllUsers() {
        return response()->json(Nft::with('drops', 'user')->get(), 200);
    }

    public function allNftsByUserId($user_id) {
        return response()->json(Nft::where('user_id', $user_id)->get(), 200);
    }

    public function getNftsForSale() {
        return response()->json(Nft::where('price', '!=', 'NULL')->has('drops', 0)->get(), 200);
    }

    public function sendNftAsGift(Nft $nft, $address) {
        $flowAccount = FlowAccount::where('address', $address)->first();

        $nft->update([
            'user_id' => $flowAccount->user_id,
            'pack_id' => NULL,
            'flow_account_id' => $flowAccount->id,
            'price' => NULL
        ]);
        $nft->drops()->detach();

        return response()->json($nft, 200);
    }

    public function store(NftRequest $request, NftService $nftService) {
        return $nftService->createNft($request);
    }

    public function synchronizationNfts(Request $request) {

        foreach ($request->nfts as $nft) {
            if (Nft::where('flow_id', $nft['id'])->first()) continue;

            $nftData = [
                'token_uri' => $nft['token_uri'],
                'flow_id' => $nft['id'],
                'user_id' => Auth::id(),
                'flow_account_id' => Auth::user()->flow_account_id,
            ];

            if ($request->address) {
                $flowAccount = FlowAccount::where('address', $request->address)->first();
                $nftData['user_id'] = $flowAccount->user_id;
                $nftData['flow_account_id'] = $flowAccount->id;
            }

            if (isset($nft['price'])) $nftData['price'] = $nft['price'];

            Nft::create($nftData);
        }

        return true;
    }

    public function show(Nft $nft) {
        if (isset($nft->metadata->attributes)) { // get author_address
            $keyAuthor = array_search('author', array_column($nft->metadata->attributes, 'attribute_type'));
            $author = (array) $nft->metadata->attributes[$keyAuthor];
            if (is_integer($keyAuthor) && $author = Author::where('name', $author['attribute_value'])->first()) {
                $nft->author_address = $author->address;
            }
        }
        if (!isset($nft->author_address)) {
            $nft->author_address = Author::where('name', 'default')->pluck('address')->first();
        }

        return response()->json($nft->load('user', 'drops'), 200);
    }
}
