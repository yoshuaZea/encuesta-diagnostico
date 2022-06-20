<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OpcionesEncuestaCalidad extends Model {

    protected $table = 'opciones_encuesta_calidad';

    protected $fillable = [ 'nombre', 'icono', 'status' ];

}
