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
    public function index(\Illuminate\Http\Request $r)
{
    $q        = $r->query('q');
    $state    = $r->query('state'); // current | returned | all
    $userId   = $r->query('user_id');
    $assetId  = $r->query('asset_id');
    $from     = $r->query('from');  // YYYY-MM-DD
    $to       = $r->query('to');    // YYYY-MM-DD

    $rows = \App\Models\AssetAssignment::with(['asset.type','user'])
        ->when($q, function($qq) use ($q) {
            $qq->where(function($w) use ($q){
                $w->whereHas('asset', function($wa) use ($q){
                    $wa->where('asset_tag','like',"%$q%")
                       ->orWhere('serial_number','like',"%$q%")
                       ->orWhere('brand','like',"%$q%")
                       ->orWhere('model','like',"%$q%");
                })->orWhereHas('user', function($wu) use ($q){
                    $wu->where('name','like',"%$q%")
                       ->orWhere('email','like',"%$q%");
                });
            });
        })
        ->when($userId, fn($qq)=>$qq->where('user_id',$userId))
        ->when($assetId, fn($qq)=>$qq->where('asset_id',$assetId))
        ->when($state === 'current', fn($qq)=>$qq->whereNull('returned_at'))
        ->when($state === 'returned', fn($qq)=>$qq->whereNotNull('returned_at'))
        ->when($from, fn($qq)=>$qq->whereDate('assigned_at','>=',$from))
        ->when($to,   fn($qq)=>$qq->whereDate('assigned_at','<=',$to))
        ->orderByDesc('assigned_at')
        ->paginate(20);

    return response()->json($rows);
}

}
