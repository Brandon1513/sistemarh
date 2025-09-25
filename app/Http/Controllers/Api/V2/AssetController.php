<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Http\Requests\AssetStoreRequest;
use App\Http\Requests\AssetUpdateRequest;
use App\Models\Asset;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AssetController extends Controller
{
    public function index(Request $r){
        $q = Asset::with(['type','currentAssignment.user'])
          ->when($r->status, fn($qq)=>$qq->where('status',$r->status))
          ->when($r->type_id, fn($qq)=>$qq->where('type_id',$r->type_id))
          ->orderByDesc('created_at')->paginate(20);
        return response()->json($q);
    }

    public function store(AssetStoreRequest $req){
        $data = $req->validated();
        if(empty($data['asset_tag'])){
            $next = (int)(optional(Asset::orderByDesc('id')->first())->id ?? 0) + 1;
            $data['asset_tag'] = 'IT-'.Str::padLeft((string)$next, 6, '0');
        }
        $data['created_by'] = $req->user()->id;
        $asset = Asset::create($data);
        return response()->json($asset->load('type'), 201);
    }

    public function show(Asset $asset){
        return response()->json($asset->load(['type','assignments.user','currentAssignment.user']));
    }

    public function update(AssetUpdateRequest $req, Asset $asset){
        $asset->update($req->validated());
        return response()->json($asset->refresh()->load('type'));
    }
}
