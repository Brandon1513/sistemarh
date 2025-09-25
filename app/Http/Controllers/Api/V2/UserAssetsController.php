<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Models\User;

class UserAssetsController extends Controller
{
    public function index(User $user){
        $activos = $user->hasMany(\App\Models\AssetAssignment::class,'user_id')
          ->whereNull('returned_at')
          ->with('asset.type')
          ->get();
        return response()->json($activos);
    }
}
