<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tomas', function (Blueprint $table) {
            $table->id('id');
            $table->string('estudiantes_run');
            $table->string('docentes_run');
            $table->string('asignaturas_id');
            $table->integer('año');
            $table->integer('semestre');
            $table->string('estado');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tomas');
    }
};
