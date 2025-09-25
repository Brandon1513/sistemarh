<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReturnAssetRequest extends FormRequest {
  public function authorize(): bool { return true; }
  public function rules(): array {
    return [
      'condition_in' => 'nullable|in:new,good,fair,poor',
      'notes'        => 'nullable|string',
    ];
  }
}
