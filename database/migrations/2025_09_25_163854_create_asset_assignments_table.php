<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    Schema::create('asset_assignments', function (Blueprint $t) {
      $t->id();
      $t->foreignId('asset_id')->constrained('assets');
      $t->foreignId('user_id')->constrained('users');     // receptor
      $t->foreignId('assigned_by')->constrained('users'); // quien entrega
      $t->timestamp('assigned_at');
      $t->timestamp('returned_at')->nullable();
      $t->enum('condition_out', ['new','good','fair','poor'])->nullable();
      $t->enum('condition_in',  ['new','good','fair','poor'])->nullable();
      $t->text('notes')->nullable();
      $t->timestamps();
    });
  }
  public function down(): void {
    Schema::dropIfExists('asset_assignments');
  }
};
