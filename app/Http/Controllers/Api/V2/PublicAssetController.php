<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Http\Resources\PublicAssetResource;
use App\Models\Asset;

class PublicAssetController extends Controller
{
    public function show($id): PublicAssetResource
    {
        $asset = Asset::query()
            ->with([
                'type:id,name',
                'brandRef:id,name',
                'currentAssignment.user:id,name',
            ])
            ->findOrFail($id);

        return new PublicAssetResource($asset);
    }
}

