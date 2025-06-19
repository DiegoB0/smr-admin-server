<?php

namespace App\Http\Controllers;

use App\Http\Requests\RequisicionRequest;
use App\Models\Requisicion;
use Illuminate\Support\Facades\Log;

class RequisicionController extends Controller
{
    public function store(RequisicionRequest $request)
    {
        // Depuración: Registra los datos recibidos en los logs al inicio
        Log::info('Entrando en store para /api/requisiciones');
        Log::info('Datos recibidos en store', ['data' => $request->all()]); // Segundo argumento como array

        // Depuración adicional: Registra el cuerpo raw como array
        $rawBody = file_get_contents('php://input');
        Log::info('Cuerpo raw del request', ['raw' => $rawBody]);

        // Depuración temporal: Muestra los datos en la terminal al inicio
        // dd($request->all());

        // Obtiene los datos validados
        $validated = $request->validated();

        // Crea la requisición con los datos validados
        $requisicion = Requisicion::create($validated);

        // Maneja los detalles si existen
        if ($request->has('detalles') && !empty($request->detalles)) {
            foreach ($request->detalles as $detalle) {
                $requisicion->detalles()->create($detalle);
            }
        }

        // Carga los detalles relacionados y devuelve la respuesta
        return response()->json([
            'message' => 'Requisición guardada con éxito',
            'requisicion' => $requisicion->load('detalles')
        ], 201);
    }
}

