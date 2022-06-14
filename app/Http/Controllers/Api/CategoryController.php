<?php

namespace App\Http\Controllers\Api;

use App\Traits\Eloquent\Uploadable;
use App\Http\Requests\{SetRequest, ReleaseRequest, PackRequest};
use App\Models\{Set, Release, Pack};
use Auth;

class CategoryController extends Controller
{
    public function index() {
        $sets = Set::get()->toArray();
        $releases = Release::get()->toArray();
        $packs = Pack::get()->toArray();

        return response()->json([
            'categories' => array_merge($sets, $releases, $packs),
            'sets' => $sets,
            'releases' => $releases,
            'packs' => $packs
        ], 200);
    }

    public function getPacks() {
        return response()->json(Pack::get(), 200);
    }

    public function createSet(SetRequest $request) {
        return response()->json(Set::create($request->only('name')), 200);
    }

    public function createRelease(ReleaseRequest $request) {
        return response()->json(Release::create($request->only('name', 'set_id')), 200);
    }

    public function createPack(PackRequest $request) {
        $packData = $request->only('name', 'release_id', 'price');
        $packData['uri_file'] = Uploadable::uploadPhoto($request->file, $request->file('file')->getClientOriginalName(), Auth::id() . '_user_packs');

        return response()->json(Pack::create($packData), 200);
    }
}
