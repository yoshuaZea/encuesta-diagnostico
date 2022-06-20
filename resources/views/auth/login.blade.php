@extends('layouts.auth')

@section('content')
    <div class="col-xl-10 col-lg-12 col-md-9">

        <div class="card o-hidden border-0">
            <div class="card-body border-0 p-5">
                <!-- Nested Row within Card Body -->
                <div class="row">
                    <div class="col-lg-6 overflow-hidden d-flex justify-content-center align-items-center">
                        <img class="object-cover" src="{{ asset('img/logo.png') }}" alt="logo"/>
                    </div>
                    <div class="col-lg-6">
                        <div class="text-center">
                            <h1 class="h4 text-gray-900 mb-4"></h1>
                        </div>
                        @if (session('msg'))
                            <div class="alert alert-main" role="alert">
                                {{ session('msg') }}
                            </div>
                        @endif
                        <form action="{{ route('login.post') }}" method="post" class="user">
                            @csrf
                            <div class="form-group">
                                <input
                                    class="form-control form-control-user @error('usuario') is-invalid @enderror"
                                    type="text"
                                    id="usuario"
                                    name="usuario"
                                    placeholder="Usuario"
                                />
                                @error('usuario')
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
                            <input type="submit" class="btn btn-primary btn-user btn-block" value="Iniciar sesión">
                        </form>
                        <hr>
                        <div class="text-center">
                            <p class="small mb-0">{{ config('app.name') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection
