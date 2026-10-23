<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Si se borra una sede, el empleado queda sin sede en lugar de borrarse
            $table->foreignId('sede_id')->nullable()->after('departamento_id')
                ->constrained('sedes')->nullOnDelete();
        });

        // Punto de partida: todos en Guadalajara; RH corrige a los de CDMX y Monterrey
        $guadalajara = DB::table('sedes')->where('nombre', 'Guadalajara')->value('id');
        DB::table('users')->update(['sede_id' => $guadalajara]);
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('sede_id');
        });
    }
};