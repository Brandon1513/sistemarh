<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AssetUpdateRequest extends FormRequest {
  public function authorize(): bool { return true; }
  public function rules(): array
{
    $id = $this->route('asset')?->id;
    return [
        'asset_tag'      => 'sometimes|string|max:50|unique:assets,asset_tag,' . $id,
        'type_id'        => 'sometimes|exists:asset_types,id',
        'brand_id'       => 'sometimes|nullable|exists:brands,id',
        'model'          => 'sometimes|nullable|string|max:120',
        'serial_number'  => 'sometimes|nullable|string|max:120',
        'condition'      => 'sometimes|string|in:new,good,fair,poor,Bueno,Regular,Malo',
        'notes'          => 'sometimes|nullable|string',
        'status'         => 'sometimes|string|in:in_stock,assigned,retired,repair',
        'purchase_date'  => 'sometimes|nullable|date',
        'purchase_cost'  => 'sometimes|nullable|numeric|min:0|max:99999999.99',
        'invoice'        => 'sometimes|nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
    ];
}

}
