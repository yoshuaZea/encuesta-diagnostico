<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\Classes\Wailon;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Notifications\ResetPassword;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class LoginController extends Controller {
    public function __construct(){
        $this->middleware('guest')->except('logout');
    }

    public function index(){
        return view('auth.login');
    }

    public function login(Request $request){
        // Realizar validación
        $credentials = $request->validate([
            'usuario' => 'required|string',
            'password' => 'required|string'
        ]);

        //
        $usuario = $credentials['usuario'];

        // Login por correo
        if(Str::contains($usuario, '@')) {
            Auth::logout();
            return back()->withErrors(['usuario' => trans('auth.failed')]);
        }
        // Login por usuario
        else {
            // Validación extra
            $credentials = array_merge($credentials, $request->validate(['usuario' => 'required|min:4|max:50']));

            // Autenticar por usuario
            if(!Auth::attempt(['usuario' => $credentials['usuario'], 'password' => $credentials['password']])){
                Auth::logout();
                return back()->withErrors(['usuario' => trans('auth.failed')]);
            }
        }

        // Verificar que el usuario se autenticó correctamente
        if(Auth::check()){
            return redirect()->route('reports.index');
        } else {
            Auth::logout();
            return back()->withErrors(['usuario' => trans('auth.failed')]);
        }
    }

    public function logout(){
        Auth::logout();

        return redirect('/');
    }
}

