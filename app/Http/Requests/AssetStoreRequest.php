<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AssetStoreRequest extends FormRequest {
  public function authorize(): bool { return true; }
  public function rules(): array
  {
      return [
          'asset_tag'      => 'nullable|string|max:50|unique:assets,asset_tag',
          'type_id'        => 'required|exists:asset_types,id',
          'brand_id'       => 'nullable|exists:brands,id',
          'model'          => 'nullable|string|max:120',
          'serial_number'  => 'nullable|string|max:120',
          'condition'      => 'required|string|in:new,good,fair,poor,Bueno,Regular,Malo',
          'notes'          => 'nullable|string',
          'status'         => 'nullable|string|in:in_stock,assigned,retired,repair',
          'purchase_date'  => 'nullable|date',
          'purchase_cost'  => 'nullable|numeric|min:0|max:99999999.99',
          'invoice'        => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120', // 5MB
          'phone_number'   => 'nullable|string|max:20',
          'carrier'        => 'nullable|string|max:50',
          'is_unlocked'    => 'nullable|boolean',
      ];
  }
}
