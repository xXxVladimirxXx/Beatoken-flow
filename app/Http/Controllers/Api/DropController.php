<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\DropRequest;
use Illuminate\Http\Request;
use App\Services\TransactionHistoryService;
use Illuminate\Support\Facades\Mail;
use App\Mail\DropBought;
use App\Models\{DropLine, Nft, Drop};
use Carbon\Carbon;
use Auth;

class DropController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:author')->only('store');
    }

    public function index() {
        return response()->json(Drop::where(['status' => 'active'])->get(), 200);
    }

    public function allDropsByUser() {
        return response()->json(Drop::where('user_id', Auth::id())->get(), 200);
    }

    public function store(DropRequest $request, Drop $drop) {

        $drop = $drop->createDrop($request);

        return response()->json([
            'drop' => $drop,
            'nfts' => Nft::where('user_id', Auth::id())->with('drops')->get(),
            'flowIds' => Nft::whereIn('id', json_decode($request->idNftsForDrop))->get()->pluck('flow_id')
        ], 200);
    }

    public function update(Drop $drop, Request $request) {
        $drop->setAppends([])->update($request->only(
            'name', 'release_name', 'release_start', 'release_end',
            'short_description', 'full_description', 'video_url', 'status'
        ));
        return response()->json($drop);
    }

    public function show(Drop $drop) {
	    if ($drop->status != 'active') return response()->json([], 404);
        return response()->json($drop, 200);
    }

    public function getCurrentDrop() {
        $now = Carbon::parse(Carbon::now())->addMinutes(15);
        return response()->json(Drop::where([['release_start', '<=', $now], ['release_end', '>=', Carbon::now()], 'status' => 'active'])->first(), 200);
    }

    public function buyDrop(Request $request, Drop $drop, TransactionHistoryService $transactionHistoryService) {

        $oldNfts = [];
        foreach ($request->ids as $id) {
            $nft = Nft::find($id);
            $oldNft = clone $nft->load('user');
            $oldNfts[] = $oldNft;

            $nft->update([
                'user_id' => Auth::id(),
                'pack_id' => NULL,
                'flow_account_id' => Auth::user()->flow_account_id,
                'price' => NULL
            ]);
            $nft->drops()->detach();

            $transactionHistoryService->createTransactionHistoryWhenUserBoughtNft($oldNft, $request);
        }

        DropLine::where(['drop_id' => $drop->id, 'user_id' => Auth::id()])->first()->delete();

        Mail::to($nft->user->email)
            ->send(new DropBought($drop, $oldNfts));

        return response()->json($drop, 200);
    }

    public function destroy(Drop $drop) {
        $drop->nfts()->update(['price' => Null]);
        $drop->nfts()->detach();
        $drop->dropLines()->delete();
        $drop->delete();
        return response()->json([], 200);
    }
}
