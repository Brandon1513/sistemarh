<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dias_inhabiles', function (Blueprint $table) {
            $table->id();
            // Vacío = aplica a todas las sedes
            $table->foreignId('sede_id')->nullable()->constrained('sedes')->cascadeOnDelete();
            $table->string('nombre');
            $table->unsignedTinyInteger('mes');
            $table->unsignedTinyInteger('dia');
            // Vacío = se repite cada año
            $table->unsignedSmallInteger('anio')->nullable();
            $table->timestamps();

            $table->index(['mes', 'dia']);
        });

        $sede = fn (string $nombre) => DB::table('sedes')->where('nombre', $nombre)->value('id');

        DB::table('dias_inhabiles')->insert([
            [
                'sede_id' => $sede('Guadalajara'), 'nombre' => 'Virgen de Zapopan',
                'mes' => 10, 'dia' => 12, 'anio' => null,
                'created_at' => now(), 'updated_at' => now(),
            ],
            [
                'sede_id' => $sede('CDMX'), 'nombre' => 'Virgen de Guadalupe',
                'mes' => 12, 'dia' => 12, 'anio' => null,
                'created_at' => now(), 'updated_at' => now(),
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('dias_inhabiles');
    }
};