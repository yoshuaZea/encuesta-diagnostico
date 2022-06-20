@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-sm-12 col-md-6 col-lg-7">
                <h3 class="text-center">Descarga de reportes</h3>
                <p class="text-dark text-justify">
                    Para descargar la información de encuesta de calidad es necesario seleccionar fecha de inicio y fecha final, posteriormente has clic en el botón descargar.
                </p>
                <form
                    action="{{ route('reports.download') }}"
                    method="post"
                    id="form_exportar"
                >
                    @csrf
                    <div class="row justify-content-center mb-3">
                        <div class="col-auto">
                            <div class="form-group">
                                <label class="@error('tipo_reporte') 'text-danger' @enderror" for="text"><b>Tipo de reporte:</b></label>
                                <select
                                    class="form-control required @error('tipo_reporte') 'is-invalid' @enderror"
                                    id="tipo_reporte"
                                    name="tipo_reporte"
                                >
                                    <option selected disabled>Selecciona</option>
                                    @foreach ($tipos as $tipo)
                                        <option
                                            value="{{ $tipo->id }}"
                                            {{ old('tipo_reporte') == $tipo->id ? 'selected' : null }}
                                        >{{ $tipo->value }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @error('tipo_reporte')
                                <div id="msg-error" class="d-block text-danger text-xs mt-2">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-auto">
                            <label
                                class="@error('fecha_inicio') 'text-danger' @enderror"
                                for="fecha_inicio"><b><i class="far fa-calendar-alt mr-1"
                            ></i> Fecha de inicio</b></label>
                            <input
                                type="date"
                                class="form-control required @error('fecha_inicio') 'is-invalid' @enderror"
                                id="fecha_inicio"
                                name="fecha_inicio"
                                min="2022-04-01"
                                max="{{ date('Y-m-d') }}"
                                value="{{ old('fecha_inicio') }}"
                                placeholder="dd/mm/aaaa"
                                autocomplete="off"
                            >
                            @error('fecha_inicio')
                                <div id="msg-error" class="d-block text-danger text-xs mt-2">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-auto">
                            <label
                                class="@error('fecha_fin') 'text-danger' @enderror"
                                for="fecha_fin"><b><i class="far fa-calendar-alt mr-1"
                            ></i> Fecha fin</b></label>
                            <input
                                type="date"
                                class="form-control required @error('fecha_fin') 'is-invalid' @enderror"
                                id="fecha_fin"
                                name="fecha_fin"
                                placeholder="dd/mm/aaaa"
                                min="2022-04-01"
                                max="{{ date('Y-m-d') }}"
                                value="{{ old('fecha_fin') }}"
                                autocomplete="off"
                            >
                            @error('fecha_fin')
                                <div id="msg-error" class="d-block text-danger text-xs mt-2">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="row justify-content-center">
                        <button type="submit" id="btnSubmit" class="btn btn-primary mx-1"><i class="fas fa-download"></i> Descargar</button>
                    </div>
                </form>
                <hr>
                <form
                    action="{{ route('reports.download') }}"
                    method="post"
                    id="form_exportar"
                >
                    @csrf
                    <p class="text-dark text-center">
                        Toma en cuenta que descargar todos los registros puede demorar un poco más de tiempo.
                    </p>
                    <div class="row justify-content-center">
                        <div class="col-auto">
                            <div class="form-group">
                                <label class="@error('tipo_reporte_general') 'text-danger' @enderror" for="text"><b>Tipo de reporte para toda la base:</b></label>
                                <select
                                    class="form-control required @error('tipo_reporte_general') 'is-invalid' @enderror"
                                    id="tipo_reporte_general"
                                    name="tipo_reporte_general"
                                >
                                    <option selected disabled>Selecciona</option>
                                    @foreach ($tipos as $tipo)
                                        <option
                                            value="{{ $tipo->id }}"
                                            {{ old('tipo_reporte_general') == $tipo->id ? 'selected' : null }}
                                        >{{ $tipo->value }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @error('tipo_reporte_general')
                                <div id="msg-error" class="d-block text-danger text-xs mt-2">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="row justify-content-center">
                        <input type="hidden" name="all" value="all">
                        <div class="col-auto">
                            <div class="form-group">
                                <button type="submit" id="btnSubmit" class="btn btn-outline-primary mx-1 d-block"><i class="fas fa-download"></i> Descargar todo</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('js/reportes.js') }}" defer></script>
@endsection
