<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RequisicionRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Cambia a true si no estás manejando autorización por roles aún
        return true;
    }

    public function rules(): array
    {
        return [
            'proveedor' => 'required|string',
            'equipo' => 'required|string',
            'no_economico' => 'required|string',
            'hrm' => 'required|integer',
            'solicita' => 'required|string',
            'concepto' => 'required|string',
            'metodo_pago' => 'required|in:orden de compra,pago',
            'prioridad' => 'required|in:baja,media,alta',
            'ubicacion' => 'required|string',
            'con_cargo_a' => 'required|string',
            'hrm_ultimo_servicio' => 'required|integer',
            'hrm_proximo_servicio' => 'required|integer',
            'hrs_faltantes' => 'required|integer',
            'estatus_entrega' => 'required|string',
            'no_rcp' => 'required|integer',
            'fecha' => 'required|date',
            'datos_adicionales' => 'nullable|string',
            'solicitante_firma' => 'required|string',
            'autoriza_firma' => 'required|string',
            'vo_bo_firma' => 'required|string',

            // Validación para los detalles
            'detalles' => 'required|array|min:1',
            'detalles.*.numero_parte' => 'required|string',
            'detalles.*.cantidad' => 'required|integer',
            'detalles.*.unidad' => 'required|string',
            'detalles.*.descripcion' => 'required|string',
            'detalles.*.cargo' => 'required|string',
            'detalles.*.precio_unitario' => 'required|numeric',
            'detalles.*.subtotal' => 'required|numeric',
            'detalles.*.iva' => 'required|numeric',
            'detalles.*.total' => 'required|numeric',
        ];
    }
}
