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
        return response()->stream(function () {
            // Envía un mensaje inicial para mantener la conexión abierta
            echo ": " . PHP_EOL;
            ob_flush();
            flush();
         $notifications = System::all();
            // Simula eventos en un bucle infinito
            while (true) {
                // Genera un mensaje (puedes personalizar esto)
                $message = json_encode([
                    'time' => now()->toDateTimeString(),
                    'message' =>   $notifications
                ]);

                // Envía el mensaje al cliente
                echo "data: $message" . PHP_EOL . PHP_EOL;
                ob_flush();
                flush();

                // Espera un segundo antes de enviar el siguiente mensaje
                sleep(30);
            }
        }, 200, [
            'Content-Type' => 'text/event-stream',
            'Cache-Control' => 'no-cache',
            'Connection' => 'keep-alive',
        ]);
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

