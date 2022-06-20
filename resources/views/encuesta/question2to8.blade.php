@extends('layouts.app')

@section('content')
    @include('ui.headers-survey', ['showPercent' => true])

    <div class="container">
        <form action="{{ route('survey.update', ['uuid' => $encuesta->uuid, 'pregunta' => request()->query('pregunta')]) }}" method="POST" id="form-survey-questions">
            @csrf
            @method('put')
            <input type="hidden" name="porcentaje" value="{{ old('porcentaje') }}">

            <div class="row justify-content-center align-items-center" id="questions">
                <div class="col-12 col-md-12">
                    <p class="text-center font-weight-bold h5 my-4">{{ $pregunta->nombre }}</p>
                </div>
                <div class="col-12 col-md-12">
                    <div class="form-group d-flex flex-row justify-content-center">
                        <div class="btn-group btn-group-toggle gap-1 flex-wrap" data-toggle="buttons">
                            @foreach ($respuestas as $key => $respuesta)
                                <label class="btn btn-options rounded w-icono p-0">
                                    <input
                                        type="radio"
                                        id="respuesta_{{ $respuesta->id }}"
                                        name="respuesta"
                                        value="{{ $respuesta->id }}"
                                    >
                                    <p class="m-0 font-weight-bold">{{ $respuesta->nombre }}</p>
                                    <i class="{{ "{$respuesta->icono} icono-{$respuesta->color}" }} fa-2x"></i>
                                </label>
                            @endforeach
                        </div>
                    </div>
                    @error('respuesta')
                        <div id="msg-error" class="d-block text-danger text-xs mt-2 ml-2">{{ $message }}</div>
                    @enderror
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
