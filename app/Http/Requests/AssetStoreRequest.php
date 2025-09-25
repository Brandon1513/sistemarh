<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AssetStoreRequest extends FormRequest {
  public function authorize(): bool { return true; }
  public function rules(): array {
    return [
      'asset_tag'     => 'nullable|string|unique:assets,asset_tag',
      'type_id'       => 'required|exists:asset_types,id',
      'brand'         => 'nullable|string|max:100',
      'model'         => 'nullable|string|max:150',
      'serial_number' => 'nullable|string|max:150',
      'condition'     => 'nullable|in:new,good,fair,poor',
      'notes'         => 'nullable|string',
    ];
  }
}
