<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequisicionDetalle extends Model
{
    protected $table = 'requisicion_detalles';

    protected $fillable = [
        'requisicion_id',
        'numero_parte',
        'cantidad',
        'unidad',
        'descripcion',
        'cargo',
        'precio_unitario',
        'subtotal',
        'iva',
        'total'
    ];

    public function requisicion()
    {
        return $this->belongsTo(Requisicion::class, 'requisicion_id');
    }
}
