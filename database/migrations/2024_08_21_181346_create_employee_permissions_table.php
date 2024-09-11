<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('employee_permissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Empleado que solicita el permiso
            $table->string('position'); // Puesto del empleado
            $table->foreignId('department_id')->constrained()->onDelete('cascade'); // Departamento del empleado
            $table->time('official_schedule'); // Horario oficial de trabajo
            $table->time('entry_exit_time'); // Hora de entrada o salida solicitada
            $table->date('date'); // Fecha del permiso
            $table->text('reason'); // Motivo del permiso
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null'); // Aprobación del supervisor
            $table->foreignId('hr_id')->nullable()->constrained('users')->onDelete('set null'); // Reconocimiento de Recursos Humanos
            $table->enum('status', ['pendiente', 'aprobado', 'rechazado'])->default('pendiente'); // Estado de la solicitud de permiso
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employee_permissions', function (Blueprint $table) {
            $table->dropColumn([
                'position', 
                'department_id', 
                'official_schedule', 
                'entry_exit_time', 
                'date', 
                'reason', 
                'approved_by', 
                'hr_id', 
                'status'
            ]);
        });

    }
};