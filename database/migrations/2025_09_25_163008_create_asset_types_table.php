<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    Schema::create('asset_types', function (Blueprint $t) {
      $t->id();
      $t->string('name')->unique(); // Teléfono, Laptop, etc.
      $t->timestamps();
    });
  }
  public function down(): void {
    Schema::dropIfExists('asset_types');
  }
};
