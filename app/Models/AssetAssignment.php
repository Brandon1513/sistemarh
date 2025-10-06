<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class AssetAssignment extends Model
{
    protected $fillable = [
        'asset_id','user_id','assigned_by','assigned_at','returned_at',
        'condition_out','condition_in','notes'
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
        'returned_at' => 'datetime',
    ];

    public function asset(){ return $this->belongsTo(Asset::class); }
    public function user(){ return $this->belongsTo(User::class); }
    public function asignador(){ return $this->belongsTo(User::class, 'assigned_by'); }
}
