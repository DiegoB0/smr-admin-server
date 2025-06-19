<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('users', function (Blueprint $table) {
        if (!Schema::hasColumn('users', 'created_at')) {
            $table->timestamp('created_at')->nullable();
        }

        if (!Schema::hasColumn('users', 'updated_at')) {
            $table->timestamp('updated_at')->nullable();
        }
    });
}


};
   









