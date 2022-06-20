<?php

use Illuminate\Database\Seeder;
use App\Models\OpcionesEncuestaCalidad as Option;

class OpcionesEncuestaCalidad extends Seeder {
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(){
        Option::create([
            'nombre' => 'Excelente',
            'icono' => 'fa-solid fa-face-grin',
            'imagen' => 'excelente.jpg',
            'color' => 'green'
        ]);
        Option::create([
            'nombre' => 'Bueno',
            'icono' => 'fa-solid fa-face-smile',
            'imagen' => 'bien.jpg',
            'color' => 'yellow'
        ]);
        Option::create([
            'nombre' => 'Regular',
            'icono' => 'fa-solid fa-face-meh',
            'imagen' => 'regular.jpg',
            'color' => 'orange'
        ]);
        Option::create([
            'nombre' => 'Malo',
            'icono' => 'fa-solid fa-face-frown',
            'imagen' => 'mal.jpg',
            'color' => 'red'
        ]);
    }
}
