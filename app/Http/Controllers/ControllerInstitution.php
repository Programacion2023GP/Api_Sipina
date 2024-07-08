<?php

namespace App\Http\Controllers;

use App\Models\Institution;
use Illuminate\Http\Request;
use App\Models\ObjResponse;
use Illuminate\Http\Response;

class ControllerInstitution extends Controller
{
    public function created(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string',
            ]);
            $institution = Institution::create([
                'name' => $request->name,
            ]);

            // Generar token de acceso

            return response()->json(ObjResponse::CorrectResponse() + ['message' => 'creada exitosamente'], 200);
        } catch (\Exception $e) {
            error_log('Error al registrar usuario: ' . $e->getMessage());
            return response()->json(ObjResponse::CatchResponse('Ocurrió un error al registrar el usuario.'), 400);
        }
    }
    public function update(Request $request, int $id)
    {
        try {
            $request->validate([
                'name' => 'required|string',
            ]);
            $institution = Institution::find($id);
            $institution->name = $request->name;
            $institution->save();

            // Generar token de acceso

            return response()->json(ObjResponse::CorrectResponse() + ['message' => 'actualizada exitosamente'], 200);
        } catch (\Exception $e) {
            error_log('Error al registrar usuario: ' . $e->getMessage());
            return response()->json(ObjResponse::CatchResponse('Ocurrió un error al registrar el usuario.'), 400);
        }
    }
    public function index(Request $request)
    {
        try {
            $institution = Institution::where('active', 1)->get();
            return response()->json(ObjResponse::CorrectResponse() + ['data' => $institution], 200);
        } catch (\Exception $e) {
            error_log('Ocurrió un error: ' . $e->getMessage());
            return response()->json(ObjResponse::CatchResponse('Ocurrió un error.'), 400);
        }
    }
    public function destroy(Request $request,int $id){
        try {
            $institution = Institution::find($id);
            $institution->active = 0;
            $institution->save();
            return response()->json(ObjResponse::CorrectResponse() + ['message' => 'Institución eliminada correctamente'], 200);
        } catch (\Exception $e) {
            error_log('Ocurrió un error: ' . $e->getMessage());
            return response()->json(ObjResponse::CatchResponse('Ocurrió un error.'), 400);
        }
    }
    public function show(Response $response)
    {
        $response->data = ObjResponse::DefaultResponse();

        try {
            $institutions = Institution::select('name as text', 'id')->where('active',1)->get();

            // Convertir el ID a número
            $institutions = $institutions->map(function ($item) {
                $item->id = (int)$item->id;
                return $item;
            });

            return response()->json(ObjResponse::CorrectResponse() + ['data' => $institutions], 200);

        } catch (\Exception $ex) {
            $response->data = ObjResponse::CatchResponse($ex->getMessage(),400);
        }

        return response()->json($response, $response->data["status_code"]);
    }
}
