<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('requisiciones', function (Blueprint $table) {
            $table->id();
            $table->string('proveedor');
            $table->string('equipo');
            $table->string('no_economico');
            $table->float('hrm');
            $table->string('solicita');
            $table->text('concepto');
            $table->enum('metodo_pago', ['orden de compra', 'pago']);
            $table->enum('prioridad', ['baja', 'media', 'alta']);
            $table->string('ubicacion');
            $table->string('con_cargo_a');
            $table->float('hrm_ultimo_servicio');
            $table->float('hrm_proximo_servicio');
            $table->float('hrs_faltantes');
            $table->string('estatus_entrega');
            $table->integer('no_rcp');
            $table->date('fecha');
            $table->text('datos_adicionales')->nullable();
            $table->string('solicitante_firma');
            $table->string('autoriza_firma');
            $table->string('vo_bo_firma');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('requisiciones');
    }
};
