<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BrandController extends Controller {
  public function index() {
    return \App\Models\Brand::orderBy('name')->get(['id','name']);
  }
  public function store(Request $r) {
    $data = $r->validate(['name' => 'required|string|max:120|unique:brands,name']);
    return \App\Models\Brand::create($data);
  }
}