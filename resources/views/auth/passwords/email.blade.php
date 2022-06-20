@extends('layouts.auth')

@section('content')
    <div class="col-xl-10 col-lg-12 col-md-9">

        <div class="card o-hidden border-0 shadow-lg my-5">
            <div class="card-body p-0">
                <!-- Nested Row within Card Body -->
                <div class="row">
                    <div class="col-lg-6 overflow-hidden">
                        <img class="w-100 h-100 object-cover" src="{{ asset('img/bus.jpg') }}" alt="bus"/>
                    </div>
                    <div class="col-lg-6">
                        <div class="p-5">
                            <div class="text-center">
                                <h1 class="h4 text-gray-900 mb-2">¿Olvidaste tu contraseña?</h1>
                                <p class="mb-4">Entendemos que perder tu contraseña es difícil, sólo tienes que introducir tu dirección de correo electrónico a continuación ¡te enviaremos un enlace para restablecer tu contraseña!</p>
                            </div>
                            <form action="{{ route('password.request') }}" method="post" class="user">
                                @csrf
                                <div class="form-group">
                                    <input
                                        type="email"
                                        class="form-control form-control-user @error('email') is-invalid @enderror"
                                        id="email"
                                        name="email"
                                        aria-describedby="emailHelp"
                                        placeholder="Ingresa tu correo electrónico..."
                                    >
                                    @error('email')
                                        <span class="invalid-feedback ml-3" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <input type="submit" class="btn btn-primary btn-user btn-block" value="Reestablecer contraseña">
                            </form>
                            <hr>
                            <div class="text-center">
                                <a class="small" href="{{ route('login') }}">Iniciar sesión</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection
