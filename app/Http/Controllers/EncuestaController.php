<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\EncuestaCalidad;
use Illuminate\Support\Facades\DB;
use App\Models\OpcionesEncuestaCalidad;

class EncuestaController extends Controller {

    public function index(){

        // Survey percent
        $percent = 0;

        return view('encuesta.index', compact('percent'));
    }

    public function store(Request $request){

        $data = $request->validate([
            'nombre' => 'required|string|min:3|max:250',
            'email' => 'required|email',
        ]);

        $encuesta = EncuestaCalidad::create([
            'uuid' => Str::uuid(),
            'nombre' => Str::title($request->nombre),
            'correo' => Str::lower($request->email),
        ]);

        return redirect()->route('survey.questions', ['uuid' => $encuesta->uuid, 'pregunta' => 1]);
    }

    public function questions(Request $request, $uuid){
        $question = $request->query('pregunta');

        // Buscar encuesta válida
        $encuesta = EncuestaCalidad::where('uuid', $uuid)->where('finalizada', false)->first();

        if(!$encuesta){
            return redirect()->route('survey.index');
        }

        // Calcular porcentaje
        $percent = round((100 / 12) * ($question + 1), 2);

        if($question == 1){
            // Elements to form
            $elements = $this->elementsForm();
            extract($elements);

            return view('encuesta.question1', compact('encuesta', 'estudios', 'percent'));

        } else if($question >= 2 && $question <= 8){

            // Indice de pregunta
            $question -= 2;

            // Elements to form
            $elements = $this->elementsForm();
            extract($elements);

            $pregunta = $preguntas[$question];

            $respuestas = OpcionesEncuestaCalidad::where('status', true)->get();

            return view('encuesta.question2to8', compact('encuesta', 'pregunta', 'respuestas', 'percent'));

        } else if($question == 9){

            return view('encuesta.question9', compact('encuesta', 'percent'));

        } else if($question == 10){
            // Elements to form
            $elements = $this->elementsForm();
            extract($elements);

            $elecciones = $elecciones->sortBy('nombre');

            return view('encuesta.question10', compact('encuesta', 'percent', 'elecciones'));
        } else {
            return redirect()->route('survey.index');
        }
    }

    public function finish(){

        $percent = 100;
        $finish = true;

        return view('encuesta.finish', compact('percent', 'finish'));
    }

    public function update(Request $request, $uuid){
        $question = $request->query('pregunta');

        // Buscar encuesta válida
        $encuesta = EncuestaCalidad::where('uuid', $uuid)->where('finalizada', false)->firstOrFail();

        if($question == 1){
            return $this->updateOne($request, $encuesta);

        } else if($question >= 2 && $question <= 8){

            return $this->updateTwoToEight($request, $encuesta);

        } else if($question == 9){

            return $this->updateNine($request, $encuesta);

        } else if($question == 10){

            return $this->updateTen($request, $encuesta);

        } else {
            return redirect()->route('survey.index');
        }
    }

    private function updateOne($request, $encuesta){

        $elements = $this->elementsForm();
        extract($elements);

        $array = $estudios->pluck('id')->implode(',');

        $data = $request->validate([
            'porcentaje' => 'nullable|numeric',
            'estudios' => 'required|array',
            'estudios.*' => 'required|integer|in:' . $array,
            'otro' => 'nullable|string|min:3'
        ]);

        // Convertir a array
        $seleccionados = $estudios->whereIn('id', $request->estudios)->pluck('nombre');

        // Si se agregó otro
        if($request->otro){
            $seleccionados->push($request->otro);
        }

        // Guardar en el modelo
        $encuesta->estudios = $seleccionados->implode(', ');
        $encuesta->save();

        return redirect()->route('survey.questions', ['uuid' => $encuesta->uuid, 'pregunta' => 2]);
    }

    private function updateTwoToEight($request, $encuesta){

        $pregunta = $request->query('pregunta');

        $data = $request->validate([
            'respuesta' => 'required|exists:App\Models\OpcionesEncuestaCalidad,id',
        ]);

        // Si se agregó otro
        if($pregunta == 2){
            $encuesta->atencion_valet_parking = $request->respuesta;
        }

        if($pregunta == 3){
            $encuesta->atencion_recepcion = $request->respuesta;
        }

        if($pregunta == 4){
            $encuesta->atencion_caja = $request->respuesta;
        }

        if($pregunta == 5){
            $encuesta->atencion_medico_tecnico = $request->respuesta;
        }

        if($pregunta == 6){
            $encuesta->informacion_recibida = $request->respuesta;
        }

        if($pregunta == 7){
            $encuesta->equipos_medicos_instalaciones = $request->respuesta;
        }

        if($pregunta == 8){
            $encuesta->entrega_resultados = $request->respuesta;
        }

        // Guardar en el modelo
        $encuesta->save();

        return redirect()->route('survey.questions', ['uuid' => $encuesta->uuid, 'pregunta' => ++$pregunta]);
    }

    public function updateNine($request, $encuesta){
        $data = $request->validate([
            'sugerencias' => 'nullable|string|max:1000',
        ]);

        // Guardar en el modelo
        $encuesta->sugerencias_mejora = Str::ucfirst($request->sugerencias);
        $encuesta->save();

        return redirect()->route('survey.questions', ['uuid' => $encuesta->uuid, 'pregunta' => 10]);

    }

    private function updateTen($request, $encuesta){

        $elements = $this->elementsForm();
        extract($elements);

        $array = $elecciones->pluck('id')->implode(',');

        $data = $request->validate([
            'porcentaje' => 'nullable|numeric',
            'eleccion' => 'required|integer|in:' . $array,
            'otro' => 'nullable|string|min:3'
        ]);

        // Convertir a array
        $seleccionado = $elecciones->where('id', $request->eleccion)->first();

        // Guardar en el modelo
        $encuesta->eleccion_cdi = $seleccionado->nombre;

        if($request->otro){
            $encuesta->eleccion_cdi .= ", {$request->otro}";
        }

        $encuesta->finalizada = true;
        $encuesta->save();

        return redirect()->route('survey.finish');
    }

    private function elementsForm(){
        $estudios = collect([
            (object)['id' => 1, 'nombre' => 'Laboratorio'],
            (object)['id' => 2, 'nombre' => 'Tomografía'],
            (object)['id' => 3, 'nombre' => 'Mastografía'],
            (object)['id' => 4, 'nombre' => 'Rayos X'],
            (object)['id' => 5, 'nombre' => 'Cardiología'],
            (object)['id' => 6, 'nombre' => 'Ultrasonido'],
            (object)['id' => 7, 'nombre' => 'Oftalmología']
        ]);

        $elecciones = collect([
            (object)['id' => 1, 'nombre' => 'Sugerencia de su médico'],
            (object)['id' => 2, 'nombre' => 'Flyers o volantes'],
            (object)['id' => 3, 'nombre' => 'Página web CDI'],
            (object)['id' => 4, 'nombre' => 'Recomendación de un familiar o conocido'],
            (object)['id' => 5, 'nombre' => 'Radio'],
            (object)['id' => 6, 'nombre' => 'Google'],
            (object)['id' => 7, 'nombre' => 'Convenio con la empresa en la que labora o aseguradora'],
            (object)['id' => 8, 'nombre' => 'Peródico']
        ]);

        $preguntas = collect([
            (object)['id' => 1, 'nombre' => 'Atención de valet parking'],
            (object)['id' => 2, 'nombre' => 'Atención en recepción'],
            (object)['id' => 3, 'nombre' => 'Atención en caja'],
            (object)['id' => 4, 'nombre' => 'Atención del médico o técnico'],
            (object)['id' => 5, 'nombre' => 'La información recibida durante mi estudio fue:'],
            (object)['id' => 6, 'nombre' => 'Los equipos médicos e instalaciones son:'],
            (object)['id' => 7, 'nombre' => 'La entrega de mis resultados fue:']
        ]);

        return [
            'estudios' => $estudios,
            'elecciones' => $elecciones,
            'preguntas' => $preguntas,
        ];
    }

    // REPORTES
    public function report(){

        $tipos = $this->reportType();

        return view('encuesta.reports', compact('tipos'));
    }

    public function download(Request $request){

        $array = $this->reportType()->pluck('id')->implode(',');

        $request->validate([
            'tipo_reporte' => 'required_with:fecha_inicio,fecha_fin|string|min:3|in:' . $array,
            'fecha_inicio' => 'required_with:tipo_reporte,fecha_fin|date_format:Y-m-d',
            'fecha_fin' => 'required_with:tipo_reporte,fecha_inicio|date_format:Y-m-d',
            'all' => 'required_with:tipo_reporte_general',
            'tipo_reporte_general' => 'required_with:all|string|min:3|in:' . $array,
        ]);

        // Validaciones según reporte
        if($request->all){
            if($request->tipo_reporte_general === 'PRC'){
                return $this->surveyPercentsTotals($request, true);

            } else {
                return $this->surveyRecords($request, true);

            }

        } else {
            // Convertir fechas para datos exactos
            $request->fecha_inicio = "{$request->fecha_inicio} 00:00:00";
            $request->fecha_fin = "{$request->fecha_fin} 23:59:59";

            if($request->tipo_reporte === 'PRC'){
                return $this->surveyPercentsTotals($request);

            } else {
                return $this->surveyRecords($request);

            }
        }
    }

    protected function surveyRecords($request, $all = false){
        try{
            // QUERY
            if($all){
                $records = EncuestaCalidad::orderBy('created_at', 'ASC')
                                        ->with('p1', 'p2', 'p3', 'p4', 'p5', 'p6', 'p7')
                                        ->get();

            } else {
                $records = EncuestaCalidad::whereBetween('created_at', [$request->fecha_inicio, $request->fecha_fin])
                                        ->orderBy('created_at', 'ASC')
                                        ->with('p1', 'p2', 'p3', 'p4', 'p5', 'p6', 'p7')
                                        ->get();
            }

            // NOMBRE DEL ARCHIVO Y CHARSET
            $headers = array(
                "Content-type"        => "text/csv; charset=utf-8",
                "Content-Disposition" => "attachment; filename=Encuesta de calidad - ".date('dmY').".csv",
                "Pragma"              => "no-cache",
                "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
                "Expires"             => "0"
            );

            // CREAR EL CSV
            $callback = function() use($records){
                // DELIMITADOR
                $delimiter = ',';

                // SALIDA DEL ARCHIVO
                $csv = fopen('php://output', 'w');

                // ENCABEZADOS
                $firstRow = array(
                    'Folio',
                    'Nombre',
                    'Correo',
                    utf8_decode('Estudios que se realizó'),
                    utf8_decode('Atención de valet parking'),
                    utf8_decode('Atención en recepción'),
                    utf8_decode('Atención en caja'),
                    utf8_decode('Atención del médico o técnico'),
                    utf8_decode('La información recibida durante mi estudio fue'),
                    utf8_decode('Los equipos médicos e instalaciones son'),
                    utf8_decode('La entrega de mis resultados fue'),
                    utf8_decode('Sugerencias'),
                    utf8_decode('¿Por qué eligió CDI?'),
                    'Status',
                    'Fecha de registro',
                    utf8_decode('Últ. modifación')
                );

                fputcsv($csv, $firstRow, $delimiter);

                // LLENAR EL ARCHIVO CON LOS DATOS DE LA CONSULTA
                foreach($records as $val){
                    $content = array(
                        $val->uuid,
                        utf8_decode($val->nombre),
                        utf8_decode($val->correo),
                        utf8_decode($val->estudios),
                        optional($val->p1)->nombre,
                        optional($val->p2)->nombre,
                        optional($val->p3)->nombre,
                        optional($val->p4)->nombre,
                        optional($val->p5)->nombre,
                        optional($val->p6)->nombre,
                        optional($val->p7)->nombre,
                        utf8_decode($val->sugerencias_mejora),
                        utf8_decode($val->eleccion_cdi),
                        $val->finalizada ? 'Completada' : 'Incompleta',
                        $val->created_at->format('d/m/Y H:i'),
                        utf8_decode($val->updated_at->diffForHumans())
                    );

                    fputcsv($csv, $content, $delimiter);
                }

                fclose($csv);
            };

            return response()->stream($callback, 200, $headers);

        } catch(Exception $e){
            return redirect()
                    ->route('reportes.index')
                    ->with('msg', 'Hubo un problema al exportar el reporte')
                    ->with('type', 'error');
        }
    }

    protected function surveyPercentsTotals($request, $all = false){
        try{
            // QUERY
            if($all){
                $questions = DB::select("
                    SELECT CAST(1 AS SIGNED) AS '#',
                    CAST('Atención de valet parking' AS CHAR) AS pregunta,
                    opciones.nombre,
                    IFNULL(lj.total, 0) AS total,
                    CONCAT( ROUND( (total / IFNULL( (SELECT COUNT(*) FROM encuesta_calidad), 0) ) * 100, 2 ), '%' ) AS porcentaje
                    FROM opciones_encuesta_calidad AS opciones
                    LEFT JOIN (
                        SELECT COUNT(*) AS total, opc.id
                        FROM encuesta_calidad AS encuesta
                        INNER JOIN opciones_encuesta_calidad AS opc ON opc.id = encuesta.atencion_valet_parking
                        GROUP BY opc.id
                    ) AS lj ON lj.id = opciones.id

                    UNION

                    SELECT CAST(2 AS SIGNED) AS '#',
                    CAST('Atención en recepción' AS CHAR) AS pregunta,
                    opciones.nombre,
                    IFNULL(lj.total, 0) AS total,
                    CONCAT( ROUND( (total / IFNULL( (SELECT COUNT(*) FROM encuesta_calidad), 0) ) * 100, 2 ), '%' ) AS porcentaje
                    FROM opciones_encuesta_calidad AS opciones
                    LEFT JOIN (
                        SELECT COUNT(*) AS total, opc.id
                        FROM encuesta_calidad AS encuesta
                        INNER JOIN opciones_encuesta_calidad AS opc ON opc.id = encuesta.atencion_recepcion
                        GROUP BY opc.id
                    ) AS lj ON lj.id = opciones.id

                    UNION

                    SELECT CAST(3 AS SIGNED) AS '#',
                    CAST('Atención en caja' AS CHAR) AS pregunta,
                    opciones.nombre,
                    IFNULL(lj.total, 0) AS total,
                    CONCAT( ROUND( (total / IFNULL( (SELECT COUNT(*) FROM encuesta_calidad), 0) ) * 100, 2 ), '%' ) AS porcentaje
                    FROM opciones_encuesta_calidad AS opciones
                    LEFT JOIN (
                        SELECT COUNT(*) AS total, opc.id
                        FROM encuesta_calidad AS encuesta
                        INNER JOIN opciones_encuesta_calidad AS opc ON opc.id = encuesta.atencion_caja
                        GROUP BY opc.id
                    ) AS lj ON lj.id = opciones.id

                    UNION

                    SELECT CAST(4 AS SIGNED) AS '#',
                    CAST('Atención del médico o técnico' AS CHAR) AS pregunta,
                    opciones.nombre,
                    IFNULL(lj.total, 0) AS total,
                    CONCAT( ROUND( (total / IFNULL( (SELECT COUNT(*) FROM encuesta_calidad), 0) ) * 100, 2 ), '%' ) AS porcentaje
                    FROM opciones_encuesta_calidad AS opciones
                    LEFT JOIN (
                        SELECT COUNT(*) AS total, opc.id
                        FROM encuesta_calidad AS encuesta
                        INNER JOIN opciones_encuesta_calidad AS opc ON opc.id = encuesta.atencion_medico_tecnico
                        GROUP BY opc.id
                    ) AS lj ON lj.id = opciones.id

                    UNION

                    SELECT CAST(5 AS SIGNED) AS '#',
                    CAST('La información recibida durante mi estudio fue:' AS CHAR) AS pregunta,
                    opciones.nombre,
                    IFNULL(lj.total, 0) AS total,
                    CONCAT( ROUND( (total / IFNULL( (SELECT COUNT(*) FROM encuesta_calidad), 0) ) * 100, 2 ), '%' ) AS porcentaje
                    FROM opciones_encuesta_calidad AS opciones
                    LEFT JOIN (
                        SELECT COUNT(*) AS total, opc.id
                        FROM encuesta_calidad AS encuesta
                        INNER JOIN opciones_encuesta_calidad AS opc ON opc.id = encuesta.informacion_recibida
                        GROUP BY opc.id
                    ) AS lj ON lj.id = opciones.id

                    UNION

                    SELECT CAST(6 AS SIGNED) AS '#',
                    CAST('Los equipos médicos e instalaciones son:' AS CHAR) AS pregunta,
                    opciones.nombre,
                    IFNULL(lj.total, 0) AS total,
                    CONCAT( ROUND( (total / IFNULL( (SELECT COUNT(*) FROM encuesta_calidad), 0) ) * 100, 2 ), '%' ) AS porcentaje
                    FROM opciones_encuesta_calidad AS opciones
                    LEFT JOIN (
                        SELECT COUNT(*) AS total, opc.id
                        FROM encuesta_calidad AS encuesta
                        INNER JOIN opciones_encuesta_calidad AS opc ON opc.id = encuesta.equipos_medicos_instalaciones
                        GROUP BY opc.id
                    ) AS lj ON lj.id = opciones.id

                    UNION

                    SELECT CAST(7 AS SIGNED) AS '#',
                    CAST('La entrega de mis resultados fue:' AS CHAR) AS pregunta,
                    opciones.nombre,
                    IFNULL(lj.total, 0) AS total,
                    CONCAT( ROUND( (total / IFNULL( (SELECT COUNT(*) FROM encuesta_calidad), 0) ) * 100, 2 ), '%' ) AS porcentaje
                    FROM opciones_encuesta_calidad AS opciones
                    LEFT JOIN (
                        SELECT COUNT(*) AS total, opc.id
                        FROM encuesta_calidad AS encuesta
                        INNER JOIN opciones_encuesta_calidad AS opc ON opc.id = encuesta.entrega_resultados
                        GROUP BY opc.id
                    ) AS lj ON lj.id = opciones.id
                ");

                $studies = DB::select("
                    SELECT encuesta.estudios,
                    COUNT(encuesta.estudios) AS total,
                    CONCAT( ROUND( (COUNT(encuesta.eleccion_cdi) / IFNULL( (SELECT COUNT(*) FROM encuesta_calidad), 0) ) * 100, 2 ), '%' ) AS porcentaje
                    FROM encuesta_calidad AS encuesta
                    WHERE encuesta.eleccion_cdi IS NOT NULL
                    GROUP BY estudios
                ");

                $choices = DB::select("
                    SELECT encuesta.eleccion_cdi,
                    COUNT(encuesta.eleccion_cdi) AS total,
                    CONCAT( ROUND( (COUNT(encuesta.eleccion_cdi) / IFNULL( (SELECT COUNT(*) FROM encuesta_calidad), 0) ) * 100, 2 ), '%' ) AS porcentaje
                    FROM encuesta_calidad AS encuesta
                    WHERE encuesta.eleccion_cdi IS NOT NULL
                    GROUP BY eleccion_cdi
                ");

            } else {
                $questions = DB::select("
                    SELECT CAST(1 AS SIGNED) AS '#',
                    CAST('Atención de valet parking' AS CHAR) AS pregunta,
                    opciones.nombre,
                    IFNULL(lj.total, 0) AS total,
                    CONCAT( ROUND( (total / IFNULL( (SELECT COUNT(*) FROM encuesta_calidad), 0) ) * 100, 2 ), '%' ) AS porcentaje
                    FROM opciones_encuesta_calidad AS opciones
                    LEFT JOIN (
                        SELECT COUNT(*) AS total, opc.id
                        FROM encuesta_calidad AS encuesta
                        INNER JOIN opciones_encuesta_calidad AS opc ON opc.id = encuesta.atencion_valet_parking
                        WHERE encuesta.created_at BETWEEN :del1 AND :al1
                        GROUP BY opc.id
                    ) AS lj ON lj.id = opciones.id

                    UNION

                    SELECT CAST(2 AS SIGNED) AS '#',
                    CAST('Atención en recepción' AS CHAR) AS pregunta,
                    opciones.nombre,
                    IFNULL(lj.total, 0) AS total,
                    CONCAT( ROUND( (total / IFNULL( (SELECT COUNT(*) FROM encuesta_calidad), 0) ) * 100, 2 ), '%' ) AS porcentaje
                    FROM opciones_encuesta_calidad AS opciones
                    LEFT JOIN (
                        SELECT COUNT(*) AS total, opc.id
                        FROM encuesta_calidad AS encuesta
                        INNER JOIN opciones_encuesta_calidad AS opc ON opc.id = encuesta.atencion_recepcion
                        WHERE encuesta.created_at BETWEEN :del2 AND :al2
                        GROUP BY opc.id
                    ) AS lj ON lj.id = opciones.id

                    UNION

                    SELECT CAST(3 AS SIGNED) AS '#',
                    CAST('Atención en caja' AS CHAR) AS pregunta,
                    opciones.nombre,
                    IFNULL(lj.total, 0) AS total,
                    CONCAT( ROUND( (total / IFNULL( (SELECT COUNT(*) FROM encuesta_calidad), 0) ) * 100, 2 ), '%' ) AS porcentaje
                    FROM opciones_encuesta_calidad AS opciones
                    LEFT JOIN (
                        SELECT COUNT(*) AS total, opc.id
                        FROM encuesta_calidad AS encuesta
                        INNER JOIN opciones_encuesta_calidad AS opc ON opc.id = encuesta.atencion_caja
                        WHERE encuesta.created_at BETWEEN :del3 AND :al3
                        GROUP BY opc.id
                    ) AS lj ON lj.id = opciones.id

                    UNION

                    SELECT CAST(4 AS SIGNED) AS '#',
                    CAST('Atención del médico o técnico' AS CHAR) AS pregunta,
                    opciones.nombre,
                    IFNULL(lj.total, 0) AS total,
                    CONCAT( ROUND( (total / IFNULL( (SELECT COUNT(*) FROM encuesta_calidad), 0) ) * 100, 2 ), '%' ) AS porcentaje
                    FROM opciones_encuesta_calidad AS opciones
                    LEFT JOIN (
                        SELECT COUNT(*) AS total, opc.id
                        FROM encuesta_calidad AS encuesta
                        INNER JOIN opciones_encuesta_calidad AS opc ON opc.id = encuesta.atencion_medico_tecnico
                        WHERE encuesta.created_at BETWEEN :del4 AND :al4
                        GROUP BY opc.id
                    ) AS lj ON lj.id = opciones.id

                    UNION

                    SELECT CAST(5 AS SIGNED) AS '#',
                    CAST('La información recibida durante mi estudio fue:' AS CHAR) AS pregunta,
                    opciones.nombre,
                    IFNULL(lj.total, 0) AS total,
                    CONCAT( ROUND( (total / IFNULL( (SELECT COUNT(*) FROM encuesta_calidad), 0) ) * 100, 2 ), '%' ) AS porcentaje
                    FROM opciones_encuesta_calidad AS opciones
                    LEFT JOIN (
                        SELECT COUNT(*) AS total, opc.id
                        FROM encuesta_calidad AS encuesta
                        INNER JOIN opciones_encuesta_calidad AS opc ON opc.id = encuesta.informacion_recibida
                        WHERE encuesta.created_at BETWEEN :del5 AND :al5
                        GROUP BY opc.id
                    ) AS lj ON lj.id = opciones.id

                    UNION

                    SELECT CAST(6 AS SIGNED) AS '#',
                    CAST('Los equipos médicos e instalaciones son:' AS CHAR) AS pregunta,
                    opciones.nombre,
                    IFNULL(lj.total, 0) AS total,
                    CONCAT( ROUND( (total / IFNULL( (SELECT COUNT(*) FROM encuesta_calidad), 0) ) * 100, 2 ), '%' ) AS porcentaje
                    FROM opciones_encuesta_calidad AS opciones
                    LEFT JOIN (
                        SELECT COUNT(*) AS total, opc.id
                        FROM encuesta_calidad AS encuesta
                        INNER JOIN opciones_encuesta_calidad AS opc ON opc.id = encuesta.equipos_medicos_instalaciones
                        WHERE encuesta.created_at BETWEEN :del6 AND :al6
                        GROUP BY opc.id
                    ) AS lj ON lj.id = opciones.id

                    UNION

                    SELECT CAST(7 AS SIGNED) AS '#',
                    CAST('La entrega de mis resultados fue:' AS CHAR) AS pregunta,
                    opciones.nombre,
                    IFNULL(lj.total, 0) AS total,
                    CONCAT( ROUND( (total / IFNULL( (SELECT COUNT(*) FROM encuesta_calidad), 0) ) * 100, 2 ), '%' ) AS porcentaje
                    FROM opciones_encuesta_calidad AS opciones
                    LEFT JOIN (
                        SELECT COUNT(*) AS total, opc.id
                        FROM encuesta_calidad AS encuesta
                        INNER JOIN opciones_encuesta_calidad AS opc ON opc.id = encuesta.entrega_resultados
                        WHERE encuesta.created_at BETWEEN :del7 AND :al7
                        GROUP BY opc.id
                    ) AS lj ON lj.id = opciones.id
                ", [
                    'del1' => $request->fecha_inicio, 'al1' => $request->fecha_fin,
                    'del2' => $request->fecha_inicio, 'al2' => $request->fecha_fin,
                    'del3' => $request->fecha_inicio, 'al3' => $request->fecha_fin,
                    'del4' => $request->fecha_inicio, 'al4' => $request->fecha_fin,
                    'del5' => $request->fecha_inicio, 'al5' => $request->fecha_fin,
                    'del6' => $request->fecha_inicio, 'al6' => $request->fecha_fin,
                    'del7' => $request->fecha_inicio, 'al7' => $request->fecha_fin
                ]);

                $studies = DB::select("
                    SELECT encuesta.estudios,
                    COUNT(encuesta.estudios) AS total,
                    CONCAT( ROUND( (COUNT(encuesta.eleccion_cdi) / IFNULL( (SELECT COUNT(*) FROM encuesta_calidad), 0) ) * 100, 2 ), '%' ) AS porcentaje
                    FROM encuesta_calidad AS encuesta
                    WHERE encuesta.eleccion_cdi IS NOT NULL
                    AND encuesta.created_at BETWEEN :del AND :al
                    GROUP BY estudios
                ", ['del' => $request->fecha_inicio, 'al' => $request->fecha_fin]);

                $choices = DB::select("
                    SELECT encuesta.eleccion_cdi,
                    COUNT(encuesta.eleccion_cdi) AS total,
                    CONCAT( ROUND( (COUNT(encuesta.eleccion_cdi) / IFNULL( (SELECT COUNT(*) FROM encuesta_calidad), 0) ) * 100, 2 ), '%' ) AS porcentaje
                    FROM encuesta_calidad AS encuesta
                    WHERE encuesta.eleccion_cdi IS NOT NULL
                    AND encuesta.created_at BETWEEN :del AND :al
                    GROUP BY eleccion_cdi
                ", ['del' => $request->fecha_inicio, 'al' => $request->fecha_fin]);
            }

            // NOMBRE DEL ARCHIVO Y CHARSET
            $headers = array(
                "Content-type"        => "text/csv; charset=utf-8",
                "Content-Disposition" => "attachment; filename=Totales y porcentajes encuesta de calidad - ".date('dmY').".csv",
                "Pragma"              => "no-cache",
                "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
                "Expires"             => "0"
            );

            // CREAR EL CSV
            $callback = function() use($questions, $studies, $choices){
                // DELIMITADOR
                $delimiter = ',';

                // SALIDA DEL ARCHIVO
                $csv = fopen('php://output', 'w');

                // ENCABEZADOS PREGUNTAS
                $firstRow = array(
                    'Pregunta',
                    'Valor',
                    'Total',
                    'Porcentaje'
                );

                fputcsv($csv, $firstRow, $delimiter);

                // LLENAR EL ARCHIVO CON LOS DATOS DE LA CONSULTA
                foreach($questions as $key => $val){
                    $content = array(
                        utf8_decode($val->pregunta),
                        $val->nombre,
                        $val->total,
                        $val->porcentaje,
                    );

                    fputcsv($csv, $content, $delimiter);
                }


                // ENCABEZADOS ESTUDIOS
                $secondRow = array(
                    'Estudios',
                    'Total',
                    'Porcentaje'
                );

                fputcsv($csv, ['', '', ''], $delimiter);
                fputcsv($csv, ['', '', ''], $delimiter);
                fputcsv($csv, $secondRow, $delimiter);

                // LLENAR EL ARCHIVO CON LOS DATOS DE LA CONSULTA
                foreach($studies as $key => $val){
                    $content = array(
                        utf8_decode($val->estudios),
                        $val->total,
                        $val->porcentaje,
                    );

                    fputcsv($csv, $content, $delimiter);
                }


                // ENCABEZADOS ELECCIONES
                $thirdRow = array(
                    utf8_decode('¿Por qué eligió CDI?'),
                    'Total',
                    'Porcentaje'
                );

                fputcsv($csv, ['', '', ''], $delimiter);
                fputcsv($csv, ['', '', ''], $delimiter);
                fputcsv($csv, $thirdRow, $delimiter);

                // LLENAR EL ARCHIVO CON LOS DATOS DE LA CONSULTA
                foreach($choices as $key => $val){
                    $content = array(
                        utf8_decode($val->eleccion_cdi),
                        $val->total,
                        $val->porcentaje,
                    );

                    fputcsv($csv, $content, $delimiter);
                }

                fclose($csv);
            };

            return response()->stream($callback, 200, $headers);

        } catch(Exception $e){
            return redirect()
                    ->route('reportes.index')
                    ->with('msg', 'Hubo un problema al exportar el reporte')
                    ->with('type', 'error');
        }
    }

    private function reportType(){
        $types = [
            (object)['id' => 'RCD', 'value' => 'Registros'],
            (object)['id' => 'PRC', 'value' => 'Totales y porcentajes']
        ];

        // Ordenar
        $types = collect($types)->sortBy('value');

        return $types;
    }
}
