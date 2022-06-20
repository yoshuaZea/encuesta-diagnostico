<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class EncuestaDeCalidad extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){

        Schema::create('opciones_encuesta_calidad', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nombre');
            $table->string('imagen')->nullable();
            $table->string('icono')->nullable();
            $table->string('color')->nullable();
            $table->boolean('status')->default(true);
            $table->timestamps();
        });

        Schema::create('encuesta_calidad', function (Blueprint $table) {
            $table->increments('id');
            $table->uuid('uuid')->nullable();
            $table->string('nombre');
            $table->string('correo')->nullable();
            $table->string('estudios')->nullable();
            $table->unsignedInteger('atencion_valet_parking')->nullable();
            $table->unsignedInteger('atencion_recepcion')->nullable();
            $table->unsignedInteger('atencion_caja')->nullable();
            $table->unsignedInteger('atencion_medico_tecnico')->nullable();
            $table->unsignedInteger('informacion_recibida')->nullable();
            $table->unsignedInteger('equipos_medicos_instalaciones')->nullable();
            $table->unsignedInteger('entrega_resultados')->nullable();
            $table->string('sugerencias_mejora', 3000)->nullable();
            $table->string('eleccion_cdi')->nullable();
            $table->boolean('finalizada')->default(false);
            $table->foreign('atencion_valet_parking')->references('id')->on('opciones_encuesta_calidad');
            $table->foreign('atencion_recepcion')->references('id')->on('opciones_encuesta_calidad');
            $table->foreign('atencion_caja')->references('id')->on('opciones_encuesta_calidad');
            $table->foreign('atencion_medico_tecnico')->references('id')->on('opciones_encuesta_calidad');
            $table->foreign('informacion_recibida')->references('id')->on('opciones_encuesta_calidad');
            $table->foreign('equipos_medicos_instalaciones')->references('id')->on('opciones_encuesta_calidad');
            $table->foreign('entrega_resultados')->references('id')->on('opciones_encuesta_calidad');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){
        Schema::dropIfExists('encuesta_calidad');
        Schema::dropIfExists('opciones_encuesta_calidad');
    }
}
