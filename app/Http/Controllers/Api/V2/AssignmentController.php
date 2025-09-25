<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Http\Requests\AssignAssetRequest;
use App\Http\Requests\ReturnAssetRequest;
use App\Models\Asset;
use App\Models\AssetAssignment;

class AssignmentController extends Controller
{
    public function assign(AssignAssetRequest $req){
        $asset = Asset::findOrFail($req->asset_id);
        if($asset->status !== 'in_stock'){
            return response()->json(['message'=>'El activo no está disponible.'],422);
        }
        $asig = AssetAssignment::create([
            'asset_id'      => $asset->id,
            'user_id'       => $req->user_id,
            'assigned_by'   => $req->user()->id,
            'assigned_at'   => now(),
            'condition_out' => $req->condition_out,
            'notes'         => $req->notes,
        ]);
        $asset->update(['status'=>'assigned']);
        return response()->json($asig->load(['asset','user']),201);
    }

    public function return(AssetAssignment $assignment, ReturnAssetRequest $req){
        if($assignment->returned_at){
            return response()->json(['message'=>'Ya devuelto.'],422);
        }
        $assignment->update([
            'returned_at'  => now(),
            'condition_in' => $req->condition_in,
            'notes'        => $req->notes,
        ]);
        $assignment->asset->update(['status'=>'in_stock']);
        return response()->json($assignment->refresh()->load(['asset','user']));
    }
}
