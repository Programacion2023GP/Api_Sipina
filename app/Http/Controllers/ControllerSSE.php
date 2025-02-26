<?php

namespace App\Http\Controllers;

use App\Models\ObjResponse;
use App\Models\System;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ControllerSSE extends Controller
{
    public function stream()
    {
        // Configurar los encabezados para la transmisión SSE
        header("Content-Type: text/event-stream");
        header("Cache-Control: no-cache");
        header("Connection: keep-alive");
        header("Access-Control-Allow-Origin: *");  // Permitir cualquier origen
        header("Access-Control-Allow-Methods: GET");  // Solo permitimos el método GET para SSE
        header("Access-Control-Allow-Headers: *");  // Permitir todos los encabezados

        // Ruta al archivo donde se guarda el mensaje

        // Verificar si el archivo existe
            // Leer el contenido del archivo

            // Enviar el mensaje como un evento SSE
            echo "event: message\n";
            echo "data: " . json_encode(['message' => 'bienvenido']) . "\n\n";
       
            sleep(1);
        // Forzar que el contenido se envíe al cliente
        ob_flush();
        flush();

        // Opcional: Si deseas mantener la conexión abierta y enviar más mensajes más tarde,
        // puedes descomentar `sleep()` para simular un retraso en el servidor.
        // sleep(1);  // Simular un retraso para la recepción del cliente.
    }
    public function create(Request $request, Response $response){
   try {
    $notification = System::create([
        'name' => $request->name,
        'rol' =>  $request->rol,
        'message' =>  $request->message,

    ]);
    return response()->json(ObjResponse::CorrectResponse() + [' notification' =>  $notification], 201);

   }catch(Exception $ex){}
   return response()->json(ObjResponse::CatchResponse($ex), 400);
    }
}

