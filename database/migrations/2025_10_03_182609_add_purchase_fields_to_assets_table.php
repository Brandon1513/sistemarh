<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void {
    Schema::table('assets', function (Blueprint $t) {
        $t->foreignId('brand_id')->nullable()->constrained('brands')->nullOnDelete();
        $t->date('purchase_date')->nullable();
        $t->decimal('purchase_cost', 12, 2)->nullable();
        $t->string('invoice_path')->nullable(); // factura subida
        // Opcional: si antes tenías brand (string), puedes dejarlo o migrarlo a brand_id
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            //
        });
    }
};
