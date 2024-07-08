<?php

namespace App\Http\Controllers;

use App\Models\ObjResponse;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ControllerUsers extends Controller
{
    public function register(Request $request)
    {
        try {

            // Validación de los datos del formulario
            $request->validate([
                'numberNomina' => 'required|integer',
                'typeUser' => 'required|integer',
                // 'institution_id' => 'required|integer',
                'name' => 'required|string',
                'lastName' => 'required|string',
                'secondSurname' => 'required|string',
                'email' => 'required|email|unique:users,email',
            ]);

            // Verificar si ya existe un usuario con el mismo número de nómina
            if (User::where('numberNomina', $request->numberNomina)->exists()) {
                return response()->json(ObjResponse::CatchResponse('El número de nómina ya está registrado.'), 400);
            }
            if (User::where('email', $request->email)->exists()) {
                return response()->json(ObjResponse::CatchResponse('El correo ya está registrado.'), 400);
            }
            // Crear el usuario
            $user = User::create([
                'numberNomina' => $request->numberNomina,
                'typeUser' => $request->typeUser,
                'name' => $request->name,
                'lastName' => $request->lastName,
                'secondSurname' => $request->secondSurname,
                'email' => $request->email,
                'institution_id' => $request->institution_id,
                'password' => Hash::make('123456'),  // Aquí deberías manejar el password de manera segura
            ]);

            // Generar token de acceso
            $token = $user->createToken($user->email)->plainTextToken;

            return response()->json(ObjResponse::CorrectResponse() + ['token' => $token], 201);
        } catch (\Exception $e) {
            error_log('Error al registrar usuario: ' . $e->getMessage());
            return response()->json(ObjResponse::CatchResponse('Ocurrió un error al registrar el usuario.'), 400);
        }
    }

    /**
     * Login de usuarios.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        try {
            // Validación de los datos del formulario
            $request->validate([
                'email' => 'required|email',
                'password' => 'required|string',
            ]);
        
            // Intento de autenticación
            if (Auth::attempt($request->only('email', 'password'))) {
                // Usuario autenticado
                $user = Auth::user();
                // Generar token de acceso
                $token = $user->createToken($user->email)->plainTextToken;
                // Incluir typeUser en la respuesta
                return response()->json(ObjResponse::CorrectResponse() + ['token' => $token, 'typeUser' => $user->typeUser], 200);
            }
        
            // Error de credenciales
            return response()->json(ObjResponse::CatchResponse('Credenciales inválidas'), 401);
        } catch (\Exception $e) {
            error_log('Error al iniciar sesión: ' . $e->getMessage());
            return response()->json(ObjResponse::CatchResponse('Ocurrió un error al iniciar sesión.'), 400);
        }
        
    }

    /**
     * Cerrar sesión del usuario actual.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        try {
            // Revocar todos los tokens de acceso del usuario
            $request->user()->tokens()->delete();

            return response()->json(ObjResponse::CorrectResponse() + ['message' => 'Sesión cerrada exitosamente'], 200);
        } catch (\Exception $e) {
            error_log('Error al cerrar sesión: ' . $e->getMessage());
            return response()->json(ObjResponse::CatchResponse('Ocurrió un error al cerrar sesión.'), 400);
        }
    }
    public function update(Request $request, int $id)
    {
        try {
            // Obtener usuario autenticado
            $user = User::find($id);

            // Validar datos del formulario
            $request->validate([
                // 'institution_id' => 'required|integer',
                'numberNomina' => 'required|integer',
                'typeUser' => 'required|integer',
                'name' => 'required|string',
                'lastName' => 'required|string',
                'secondSurname' => 'required|string',
                // 'email' => 'required|email|unique:users,email',
            ]);

            // Actualizar datos del usuario
            $user->name = $request->name;
            $user->lastName = $request->lastName;
            $user->typeUser = $request->typeUser;
            $user->numberNomina = $request->numberNomina;
            $user->institution_id = $request->institution_id ? $request->institution_id :null;
            $user->secondSurname = $request->secondSurname;
            $user->save();

            return response()->json(ObjResponse::CorrectResponse() + ['message' => 'Datos actualizados correctamente'], 200);
        } catch (\Exception $e) {
            error_log('Error al actualizar usuario: ' . $e->getMessage());
            return response()->json(ObjResponse::CatchResponse('Ocurrió un error al actualizar los datos del usuario.'), 400);
        }
    }
    public function index(Request $request)
    {
        try {
            $users = User::with(['institution' => function ($query) {
                $query->select('id', 'name');
            }])
            ->where('active', 1)
            ->get(['id', 'numberNomina', 'name', 'lastName', 'secondSurname', 'typeUser', 'email', 'institution_id']);

            // Modificar la estructura de datos si es necesario
            $formattedUsers = $users->map(function ($user) {
                $institutionName = $user->institution ? $user->institution->name : null;

                return [
                    'id' => $user->id,
                    'numberNomina' => $user->numberNomina,
                    'institution_id' => $user->institution_id,
                    'name' => $user->name,
                    'lastName' => $user->lastName,
                    'secondSurname' => $user->secondSurname,
                    'typeUser' => $user->typeUser,
                    'email' => $user->email,
                    'institution' => $institutionName,
                ];
            });
            return response()->json(ObjResponse::CorrectResponse() + ['data' => $formattedUsers], 200);
        } catch (\Exception $e) {
            error_log('Ocurrió un error: ' . $e->getMessage());
            return response()->json(ObjResponse::CatchResponse('Ocurrió un error.'), 400);
        }
    }
    public function destroy(Request $request, int $id)
    {
        try {
            $user = User::find($id);
            $user->active = 0;
            $user->save();
            return response()->json(ObjResponse::CorrectResponse() + ['message' => 'Usuario eliminado correctamente'], 200);
        } catch (\Exception $e) {
            error_log('Ocurrió un error: ' . $e->getMessage());
            return response()->json(ObjResponse::CatchResponse('Ocurrió un error.'), 400);
        }
    }
}
