<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Requisicion extends Model
{
    protected $table = 'requisiciones';

    // Otras configuraciones como $fillable, relaciones, etc.
    protected $fillable = [
        'proveedor', 'equipo', 'no_economico', 'hrm', 'solicita',
        'concepto', 'metodo_pago', 'prioridad', 'ubicacion', 'con_cargo_a',
        'hrm_ultimo_servicio', 'hrm_proximo_servicio', 'hrs_faltantes',
        'estatus_entrega', 'no_rcp', 'fecha', 'datos_adicionales',
        'solicitante_firma', 'autoriza_firma', 'vo_bo_firma'
    ];

    // Relación con detalles
    public function detalles()
    {
        return $this->hasMany(RequisicionDetalle::class, 'requisicion_id');
    }
}
