<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AssignAssetRequest extends FormRequest {
  public function authorize(): bool { return true; }
  public function rules(): array {
    return [
      'asset_id'      => 'required|exists:assets,id',
      'user_id'       => 'required|exists:users,id',
      'condition_out' => 'nullable|in:new,good,fair,poor',
      'notes'         => 'nullable|string',
    ];
  }
}
