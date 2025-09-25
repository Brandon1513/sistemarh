<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    Schema::create('assets', function (Blueprint $t) {
      $t->id();
      $t->string('asset_tag')->unique();                 // IT-000001
      $t->foreignId('type_id')->constrained('asset_types');
      $t->string('brand')->nullable();
      $t->string('model')->nullable();
      $t->string('serial_number')->nullable()->index();
      $t->enum('status', ['in_stock','assigned','repair','retired'])
        ->default('in_stock')->index();
      $t->enum('condition', ['new','good','fair','poor'])->default('good');
      $t->text('notes')->nullable();
      $t->foreignId('created_by')->constrained('users');
      $t->timestamps();
    });
  }
  public function down(): void {
    Schema::dropIfExists('assets');
  }
};
