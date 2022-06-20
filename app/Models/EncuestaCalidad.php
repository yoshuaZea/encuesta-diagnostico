<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EncuestaCalidad extends Model {

    protected $table = 'encuesta_calidad';

    protected $fillable = [
        'uuid',
        'nombre',
        'correo',
        'estudios',
        'atencion_valet_parking',
        'atencion_recepcion',
        'atencion_caja',
        'atencion_medico_tecnico',
        'informacion_recibida',
        'equipos_medicos_instalaciones',
        'entrega_resultados',
        'sugerencias_mejora',
        'eleccion_cdi',
        'finalizada'
    ];

    // RELACIONES
    public function p1(){
        return $this->belongsTo(OpcionesEncuestaCalidad::class, 'atencion_valet_parking');
    }

    public function p2(){
        return $this->belongsTo(OpcionesEncuestaCalidad::class, 'atencion_recepcion');
    }

    public function p3(){
        return $this->belongsTo(OpcionesEncuestaCalidad::class, 'atencion_caja');
    }

    public function p4(){
        return $this->belongsTo(OpcionesEncuestaCalidad::class, 'atencion_medico_tecnico');
    }

    public function p5(){
        return $this->belongsTo(OpcionesEncuestaCalidad::class, 'informacion_recibida');
    }

    public function p6(){
        return $this->belongsTo(OpcionesEncuestaCalidad::class, 'equipos_medicos_instalaciones');
    }

    public function p7(){
        return $this->belongsTo(OpcionesEncuestaCalidad::class, 'entrega_resultados');
    }
}
