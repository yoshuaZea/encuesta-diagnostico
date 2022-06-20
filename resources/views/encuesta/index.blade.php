@extends('layouts.app')

@section('content')
    @include('ui.headers-survey', ['showPercent' => false])

    <div class="container">
        <form action="{{ route('survey.store') }}" method="POST" id="form-survey">
            @csrf
            <input type="hidden" name="porcentaje" value="{{ old('porcentaje') }}">

            <div class="row justify-content-center" id="part-1">
                <div class="col-12 col-md-4">
                    <div class="form-group">
                        <label
                            class="@error('nombre') text-primary @enderror"
                            for="nombre"
                        >¿Cuál es tu nombre?</label>
                        <input
                            type="text"
                            class="form-control yup @error('nombre') is-invalid @enderror"
                            id="nombre"
                            name="nombre"
                            placeholder="Ingresa tu nombre completo"
                            value="{{ old('nombre') }}"
                        >
                        @error('nombre')
                            <div id="error-msg" class="text-primary text-sm mt-2 ml-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label
                            class="@error('email') text-primary @enderror"
                            for="email"
                        >¿Cuál es tu correo electrónico?</label>
                        <input
                            type="text"
                            class="form-control yup @error('email') is-invalid @enderror"
                            id="email"
                            name="email"
                            aria-describedby="emailHelp"
                            placeholder="correo@correo.com"
                            value="{{ old('email') }}"
                        >
                        <small id="emailHelp" class="form-text text-muted">No compartiremos tu correo con nadie más.</small>
                        @error('email')
                            <div id="error-msg" class="text-primary text-sm mt-2 ml-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-12">
                    <button
                        type="submit"
                        class="btn btn-primary d-block mx-auto"
                        id="next"
                    >Siguiente</button>
                </div>
            </div>
        </form>
    </div>
@endsection
