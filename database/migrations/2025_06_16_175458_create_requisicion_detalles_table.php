<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('requisicion_detalles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('requisicion_id')->constrained('requisiciones')->onDelete('cascade');
            $table->string('numero_parte');
            $table->integer('cantidad');
            $table->string('unidad');
            $table->string('descripcion');
            $table->string('cargo');
            $table->float('precio_unitario', 10, 2);
            $table->float('subtotal', 10, 2);
            $table->float('iva', 10, 2);
            $table->float('total', 10, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('requisicion_detalles');
    }
};
