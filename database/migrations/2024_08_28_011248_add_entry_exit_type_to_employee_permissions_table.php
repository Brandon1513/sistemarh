<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEntryExitTypeToEmployeePermissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('employee_permissions', function (Blueprint $table) {
            $table->enum('entry_exit_type', ['entrada', 'salida'])->after('entry_exit_time')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('employee_permissions', function (Blueprint $table) {
            $table->dropColumn('entry_exit_type');
        });
    }
}
