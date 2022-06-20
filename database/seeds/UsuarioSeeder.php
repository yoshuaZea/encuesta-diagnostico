<?php

use App\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsuarioSeeder extends Seeder {
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(){

        User::create([
            'nombre' => 'Job Yoshua Zea',
            'usuario' => 'jzea',
            'password' => Hash::make('ATv#$M4HitOI')
        ]);

        User::create([
            'nombre' => 'Administrador encuesta de calidad',
            'usuario' => 'admin_cdi',
            'password' => Hash::make('dEN46hAKcQ!5')
        ]);
    }
}
