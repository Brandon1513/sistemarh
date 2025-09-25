<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AssetUpdateRequest extends FormRequest {
  public function authorize(): bool { return true; }
  public function rules(): array {
    $id = $this->route('asset')->id ?? null;
    return [
      'asset_tag'     => 'sometimes|string|unique:assets,asset_tag,'.$id,
      'type_id'       => 'sometimes|exists:asset_types,id',
      'brand'         => 'sometimes|nullable|string|max:100',
      'model'         => 'sometimes|nullable|string|max:150',
      'serial_number' => 'sometimes|nullable|string|max:150',
      'status'        => 'sometimes|in:in_stock,assigned,repair,retired',
      'condition'     => 'sometimes|in:new,good,fair,poor',
      'notes'         => 'sometimes|nullable|string',
    ];
  }
}
