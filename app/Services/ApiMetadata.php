<?php

namespace App\Services;

use App\Models\{Nft, FlowAccount};
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;

class ApiMetadata
{
    public function createMetadata(Nft $nft, $nftData, $attributes = [])
    {
        try {
            $metadata = [
                'name' => $nftData['name'],
                'source_file' => Storage::disk('public')->url($nftData['uri_file']),
                'cover_image' => Storage::disk('public')->url($nftData['cover_image']),
                'external_url' => env('APP_URL') . '/nft/' . $nft->id,
                'description' => $nftData['description'],

                'address' => env('AccountNonFungibleBeatoken'),
                'contract_name' => env('NameNonFungibleBeatokenContract'),
                'token_id' => (int) $nft->flow_id
            ];

            if ([] != $attributes) $metadata['attributes'] = $attributes;

            return Http::post(env('URL_API_TOKEN_METADATA') . 'metadata/store', $metadata);
        } catch (\Exception $e) {
            $nft->delete();
            return $e->getMessage();
        }
    }
}
