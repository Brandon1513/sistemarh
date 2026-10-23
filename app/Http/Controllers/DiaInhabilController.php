<?php

namespace App\Http\Controllers;

use App\Models\DiaInhabil;
use App\Models\Sede;
use App\Support\CalendarioLaboral;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class DiaInhabilController extends Controller
{
    public function index(Request $request)
    {
        $query = DiaInhabil::with('sede')->orderBy('mes')->orderBy('dia');

        if ($request->filled('sede')) {
            $request->input('sede') === 'todas'
                ? $query->whereNull('sede_id')
                : $query->where('sede_id', $request->input('sede'));
        }

        $dias = $query->get();
        $sedes = Sede::orderBy('nombre')->get();
        $festivosDeLey = CalendarioLaboral::festivosDeLey(now()->year);
        ksort($festivosDeLey);

        return view('dias_inhabiles.index', compact('dias', 'sedes', 'festivosDeLey'));
    }

    public function create()
    {
        return view('dias_inhabiles.form', [
            'dia'   => new DiaInhabil(),
            'sedes' => Sede::orderBy('nombre')->get(),
        ]);
    }

    public function store(Request $request)
    {
        DiaInhabil::create($this->validar($request));

        return redirect()->route('dias_inhabiles.index')->with('success', 'Día inhábil registrado.');
    }

    public function edit(DiaInhabil $diaInhabil)
    {
        return view('dias_inhabiles.form', [
            'dia'   => $diaInhabil,
            'sedes' => Sede::orderBy('nombre')->get(),
        ]);
    }

    public function update(Request $request, DiaInhabil $diaInhabil)
    {
        $diaInhabil->update($this->validar($request, $diaInhabil));

        return redirect()->route('dias_inhabiles.index')->with('success', 'Día inhábil actualizado.');
    }

    public function destroy(DiaInhabil $diaInhabil)
    {
        $diaInhabil->delete();

        return redirect()->route('dias_inhabiles.index')->with('success', 'Día inhábil eliminado.');
    }

    /**
     * RH elige una fecha y si se repite; aquí se convierte a mes, día y año (vacío = cada año).
     */
    private function validar(Request $request, ?DiaInhabil $actual = null): array
    {
        $request->validate([
            'nombre'     => 'required|string|max:100',
            'fecha'      => 'required|date',
            'sede_id'    => 'nullable|exists:sedes,id',
            'recurrente' => 'nullable|boolean',
        ]);

        $fecha = Carbon::parse($request->input('fecha'));

        $datos = [
            'nombre'  => trim($request->input('nombre')),
            'sede_id' => $request->input('sede_id') ?: null,
            'mes'     => $fecha->month,
            'dia'     => $fecha->day,
            'anio'    => $request->boolean('recurrente') ? null : $fecha->year,
        ];

        // No dos días iguales para la misma sede
        $duplicado = DiaInhabil::where('mes', $datos['mes'])
            ->where('dia', $datos['dia'])
            ->where(fn ($q) => $datos['sede_id'] ? $q->where('sede_id', $datos['sede_id']) : $q->whereNull('sede_id'))
            ->when($datos['anio'] !== null, fn ($q) => $q->where(fn ($w) => $w->whereNull('anio')->orWhere('anio', $datos['anio'])))
            ->when($actual, fn ($q) => $q->whereKeyNot($actual->id))
            ->exists();

        if ($duplicado) {
            throw ValidationException::withMessages([
                'fecha' => 'Ya existe un día inhábil en esa fecha para esa sede.',
            ]);
        }

        // Tampoco uno que ya es festivo de ley (se aplicaría dos veces)
        if ($datos['anio'] !== null && array_key_exists($fecha->toDateString(), CalendarioLaboral::festivosDeLey($fecha->year))) {
            throw ValidationException::withMessages([
                'fecha' => 'Esa fecha ya es festivo de ley y se aplica sola a todas las sedes.',
            ]);
        }

        return $datos;
    }
}