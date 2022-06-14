<?php

namespace App\Services;

use App\Models\{Nft};
use App\Traits\Eloquent\Uploadable;
use Illuminate\Support\Facades\{Storage};
use Auth;

class NftService {
    public function createNft($request) {
        try {
            // Save the main token file
            $ipfs_hash = (new IPFSService)->add(file_get_contents($request->file));
            $uri_file = Uploadable::uploadPhoto($request->file,
                $ipfs_hash . '.' . $request->file('file')->getClientOriginalExtension(),
                Auth::id() . '_user_nfts'
            );

            // Save the cover image
            $cover_image = Uploadable::uploadPhoto($request->cover_image,
                $ipfs_hash . '_cover.' . $request->file('cover_image')->getClientOriginalExtension(),
                Auth::id() . '_user_nfts/covers'
            );
            if ($request->file('creator_avatar')) {
                // Save the creator avatar
                $creator_avatar = Uploadable::uploadPhoto($request->creator_avatar,
                    $ipfs_hash . '_creator.' . $request->file('creator_avatar')->getClientOriginalExtension(),
                    Auth::id() . '_user_nfts/creators'
                );
                $creator_avatar_full_uri = Storage::disk('public')->url($creator_avatar);
            }

            $nftData = $request->only('name', 'description', 'price', 'pack_id');
            $nftData['ipfs_hash'] = $ipfs_hash;
            $nftData['uri_file'] = $uri_file;
            $nftData['cover_image'] = $cover_image;
            $nftData['token_uri'] = env('URL_API_TOKEN_METADATA') . env('AccountNonFungibleBeatoken') . '/' . env('NameNonFungibleBeatokenContract') . '/';
            $nftData['user_id'] = Auth::id();
            $nftData['flow_account_id'] = Auth::user()->flow_account_id;

            $nft = Nft::create($nftData);

            $flowId = (new FlowService)->createNft($nftData);

            $nft->flow_id = $flowId;
            $nft->token_uri = $nft->token_uri . $flowId;
            $nft->save();

            $attributes = [];
            if ($request->type) $attributes[] = ['attribute_template' => 'cube', 'attribute_type' => 'type', 'attribute_value' => $request->type];
            if ($request->author) $attributes[] = ['attribute_template' => 'cube', 'attribute_type' => 'author', 'attribute_value' => $request->author];
            if ($request->creator) $attributes[] = ['attribute_template' => 'cube', 'attribute_type' => 'creator', 'attribute_value' => $request->creator];
            if (isset($creator_avatar_full_uri)) $attributes[] = ['attribute_template' => 'cube', 'attribute_type' => 'creator_avatar', 'attribute_value' => $creator_avatar_full_uri];
            if ($request->numbering) $attributes[] = ['attribute_template' => 'cube', 'attribute_type' => 'numbering', 'attribute_value' => $request->numbering];

            (new ApiMetadata)->createMetadata($nft, $nftData, $attributes);

        } catch (\Exception $e) {
            if (isset($nft)) $nft->delete();
            return response()->json($e->getMessage(), 500);
        }

        return response()->json(Nft::where('user_id', Auth::id())->with('drops')->get(), 200);
    }
}