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
                                <h1 class="h4 text-gray-900 mb-4">Confirma tu cuenta</h1>
                            </div>
                            <form method="POST" action="{{ route('confirm.account', ['token' => $usuario->reset_token, 'usuario' => ($usuario->email ?? $usuario->alias ?? $usuario->celular) ]) }}" class="user">
                                @csrf

                                <input type="hidden" name="token_confirmation" value="{{ $usuario->reset_token }}">

                                <div class="form-group row">
                                    <label class="col-md-12 col-form-label text-center font-weight-bold">Hola {{ "$usuario->nombre $usuario->apepaterno " }} ingresa una contraseña con la que accederás al sistema posteriormente.</label>
                                </div>

                                <div class="form-group">
                                    <input
                                        class="form-control form-control-user @error('email') is-invalid @enderror"
                                        type="email"
                                        id="email"
                                        name="email"
                                        aria-describedby="emailHelp"
                                        placeholder="Usuario"
                                        value="{{ $usuario->email ?? $usuario->alias ?? $usuario->celular }}"
                                        readonly
                                    />
                                    @error('email')
                                        <span class="invalid-feedback ml-3" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <input
                                        class="form-control form-control-user @error('password') is-invalid @enderror"
                                        type="password"
                                        id="password"
                                        name="password"
                                        placeholder="Contraseña"
                                    />
                                    @error('password')
                                        <span class="invalid-feedback ml-3" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <input
                                        class="form-control form-control-user @error('confirmar_password') is-invalid @enderror"
                                        type="password"
                                        id="confirmar_password"
                                        name="confirmar_password"
                                        placeholder="Confirmar contraseña"
                                    />
                                    @error('confirmar_password')
                                        <span class="invalid-feedback ml-3" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                                <input type="submit" class="btn btn-primary btn-user btn-block" value="Confirmar">
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection
