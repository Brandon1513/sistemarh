<?php

// database/migrations/2025_10_03_000001_add_phone_fields_to_assets_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('assets', function (Blueprint $t) {
            $t->string('phone_number', 20)->nullable()->index();
            $t->string('carrier', 50)->nullable();
            $t->boolean('is_unlocked')->nullable(); // null=desconocido, true=libre, false=bloqueado
        });
    }
    public function down(): void {
        Schema::table('assets', function (Blueprint $t) {
            $t->dropColumn(['phone_number','carrier','is_unlocked']);
        });
    }
};
