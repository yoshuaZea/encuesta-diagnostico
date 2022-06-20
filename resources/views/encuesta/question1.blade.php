@extends('layouts.app')

@section('content')
    @include('ui.headers-survey', ['showPercent' => true])

    <div class="container">
        <form action="{{ route('survey.update', ['uuid' => $encuesta->uuid, 'pregunta' => request()->query('pregunta')]) }}" method="POST" id="form-surve">
            @csrf
            @method('put')

            <input type="hidden" name="porcentaje" value="{{ old('porcentaje') ?? $percent }}">

            <div class="row justify-content-center my-3" id="part-2">
                <div class="col-12 col-md-5">
                    <label
                        class="h5 font-weight-bold @error('estudios') text-primary @enderror"
                        for="estudios"
                    >Estudios que se realizó:</label>
                    <div class="d-flex flex-row flex-wrap justify-content-start gap-1">
                        @foreach($estudios as $estudio)
                            <div class="srv-cool">
                                <input
                                    class="inp-cbx yup"
                                    type="checkbox"
                                    id="estudios_{{ $estudio->id }}"
                                    name="estudios[]"
                                    value="{{ $estudio->id }}"
                                    style="display: none;"
                                    {{ old("estudios.{$estudio->id}") == $estudio->id ? 'selected' : null }}
                                />
                                <label class="cbx" for="estudios_{{ $estudio->id }}">
                                    <span>
                                        <svg width="12px" height="10px" viewbox="0 0 12 10">
                                            <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                                        </svg>
                                    </span>
                                    <span>{{  $estudio->nombre }}</span>
                                </label>
                            </div>
                        @endforeach
                        <div class="d-flex flex-row align-items-center">
                            <label
                                class="mb-0 mx-2 @error('otro') text-primary @enderror"
                                for="otro"
                            >Otro:</label>
                            <input
                                type="text"
                                class="form-control form-control-sm @error('otro') is-invalid @enderror"
                                id="otro"
                                name="otro"
                                placeholder="Nombre del otro"
                                value="{{ old('otro') }}"
                            >
                            @error('otro')
                                <div id="error-msg" class="text-primary text-sm mt-2 ml-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    @error('estudios')
                        <div id="msg-error" class="d-block text-danger text-xs mt-2 ml-2">{{ $message }}</div>
                    @enderror
                    @error('estudios.*')
                        <div id="msg-error" class="d-block text-danger text-xs mt-2 ml-2">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-12 d-flex justify-content-center gap-3 mt-3">
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
