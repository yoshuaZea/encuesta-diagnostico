@extends('layouts.app')

@section('content')

    @include('ui.headers-survey', ['showPercent' => true])

    <div class="container">
        <form action="{{ route('survey.update', ['uuid' => $encuesta->uuid, 'pregunta' => request()->query('pregunta')]) }}" method="POST" id="form-survey-suggestions">
            @csrf
            @method('put')

            <input type="hidden" name="porcentaje" value="{{ old('porcentaje') }}">

            <div class="row justify-content-center my-3">
                <div class="col-12 col-md-5">
                    <div class="form-group">
                        <label
                            class="h5 font-weight-bold @error('sugerencias') text-primary @enderror"
                            for="sugerencias"
                        >¿Qué le sugiere a CDI para mejorar nuestro servicio?</label>
                        <textarea
                            type="text"
                            class="form-control yup @error('sugerencias') is-invalid @enderror"
                            id="sugerencias"
                            name="sugerencias"
                            placeholder="Escribe aquí tus sugerencias..."
                            rows="5"
                            maxlength="1000"
                        >{{ old('sugerencias') }}</textarea>
                        @error('sugerencias')
                            <div id="error-msg" class="text-primary text-sm mt-2 ml-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-12 d-flex justify-content-center gap-3 mt-3">
                    <button
                        type="button"
                        class="btn text-primary"
                        onclick="window.history.back()"
                    >Anterior</button>
                    <button
                        type="submit"
                        class="btn btn-primary"
                        id="next"
                    >Siguiente</button>
                </div>
            </div>
        </form>
    </div>
@endsection
