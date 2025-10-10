<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    protected $fillable = [
        'asset_tag',
        'type_id',
        'brand_id',        // catálogo de marcas
        'brand',           // si aún mantienes el campo texto, puedes quitarlo si ya no lo usas
        'model',
        'serial_number',
        'status',
        'condition',
        'notes',
        'purchase_date',
        'purchase_cost',
        'invoice_path',
        'phone_number',
        'carrier',
        'is_unlocked',
        'created_by',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'is_unlocked'   => 'boolean',
    ];

    public function type()
    {
        return $this->belongsTo(\App\Models\AssetType::class, 'type_id');
    }

    public function brandRef()
    {
        return $this->belongsTo(\App\Models\Brand::class, 'brand_id');
    }

    public function assignments()
    {
        return $this->hasMany(\App\Models\AssetAssignment::class);
    }

    public function currentAssignment()
    {
        return $this->hasOne(\App\Models\AssetAssignment::class)->whereNull('returned_at');
    }
}
