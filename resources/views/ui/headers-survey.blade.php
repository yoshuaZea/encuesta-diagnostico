<div class="container">
    <div class="row justify-content-center">
        <div class="col-12">
            <h1 class="text-primary text-center w-100">Encuesta de calidad</h1>
            @if(!isset($finish))
                <p class="text-secondary text-center">
                    Por favor, ayúdenos a completar esta pequeña encuesta para mejorar la calidad de nuestros servicios.
                </p>
            @endif
        </div>
        @if($showPercent)
            <div class="col-12 col-md-4">
                <p class="mb-0 text-secondary">Progreso</p>
                <div class="progress mb-3">
                    <div
                        class="progress-bar"
                        role="progressbar"
                        style="width: {{ $percent }}%;"
                        aria-valuenow="{{ $percent }}"
                        aria-valuemin="0"
                        aria-valuemax="100">{{ $percent }}%</div>
                </div>
            </div>
        @endif
        @if(isset($finish) && $finish)
            <div class="col-12">
                <p class="text-secondary text-center h4">
                    ¡Por su atención y tiempo, Gracias!
                </p>
            </div>
        @endif
    </div>
</div>
