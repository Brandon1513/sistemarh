<?php

namespace App\Models;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class DiaInhabil extends Model
{
    protected $table = 'dias_inhabiles';

    protected $fillable = ['sede_id', 'nombre', 'mes', 'dia', 'anio'];

    public function sede()
    {
        return $this->belongsTo(Sede::class);
    }

    protected static function booted(): void
    {
        static::saved(fn () => \App\Support\CalendarioLaboral::limpiarCache());
        static::deleted(fn () => \App\Support\CalendarioLaboral::limpiarCache());
    }
    /**
 * La próxima vez que cae este día (hoy o después). Para un día de un solo año, esa fecha.
 */
    public function proximaFecha(): ?Carbon
    {
        if ($this->anio) {
            return checkdate($this->mes, $this->dia, $this->anio)
                ? Carbon::create($this->anio, $this->mes, $this->dia)
                : null;
        }

        $anio = today()->year;

        // Si ya pasó este año, o la fecha no existe este año (29 de febrero), busca el siguiente
        while (! checkdate($this->mes, $this->dia, $anio) || Carbon::create($anio, $this->mes, $this->dia)->lt(today())) {
            $anio++;
        }

        return Carbon::create($anio, $this->mes, $this->dia);
    }
}