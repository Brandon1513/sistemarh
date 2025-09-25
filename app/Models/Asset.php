<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    protected $fillable = [
        'asset_tag','type_id','brand','model','serial_number',
        'status','condition','notes','created_by'
    ];

    public function type(){ return $this->belongsTo(AssetType::class, 'type_id'); }
    public function assignments(){ return $this->hasMany(AssetAssignment::class); }
    public function currentAssignment(){
        return $this->hasOne(AssetAssignment::class)->whereNull('returned_at');
    }
}
