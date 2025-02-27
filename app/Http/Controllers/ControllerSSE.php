<?php

namespace App\Http\Controllers;

use App\Models\Notification as ModelsNotification;
use App\Models\ObjResponse;
use App\Models\Role;
use App\Models\System;
use App\Models\UserReadNotification;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\DB;

class ControllerSSE extends Controller
{
    public function stream($name, $rol, $id_user)
    {
        // Configurar los encabezados para la transmisión SSE
        header("Content-Type: text/event-stream");
        header("Cache-Control: no-cache");
        header("Connection: keep-alive");
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: GET");
        header("Access-Control-Allow-Headers: *");
    
        // Obtener notificaciones no leídas por el usuario
        $notifications = DB::table('system')
            ->join('users_read_notifications', 'users_read_notifications.system_id', '=', 'system.id')
            ->join('roles', 'users_read_notifications.roles_id', '=', 'roles.id')
            ->join('notifications', 'users_read_notifications.notifications_id', '=', 'notifications.id')
            ->where('system.name', $name)
            ->where('roles.name', $rol)
            ->whereNotExists(function ($query) use ($id_user) {
                $query->select(DB::raw(1))
                    ->from('users_read_notifications as urn')
                    ->whereColumn('urn.system_id', 'system.id')
                    ->where('urn.id_user', $id_user);
            })
            ->select(
                'system.name as system_name',
                'roles.name as role_name',
                'notifications.message',
                'notifications.id as notification_id', // Seleccionar el ID de la notificación
                'users_read_notifications.created_at as read_at'
            )
            ->get();
    
        // Enviar el mensaje como un evento SSE
        echo "event: message\n";
        echo "data: " . json_encode(['message' => $notifications->toSql()]) . "\n\n";
    
        // Forzar que el contenido se envíe al cliente
        ob_flush();
        flush();
    
        // Marcar las notificaciones como leídas
        DB::beginTransaction();
        try {
            foreach ($notifications as $notification) {
                // Verificar si la notificación ya está marcada como leída
                $exists = DB::table('users_read_notifications')
                    ->where('id_user', $id_user)
                    ->where('notifications_id', $notification->notification_id)
                    ->exists();
    
                if (!$exists) {
                    DB::table('users_read_notifications')->insert([
                        'id_user' => $id_user,
                        'system_id' => DB::table('system')->where('name', $name)->value('id'),
                        'roles_id' => DB::table('roles')->where('name', $rol)->value('id'),
                        'notifications_id' => $notification->notification_id,
                        'created_at' => now(),
                    ]);
                }
            }
            DB::commit();
        } catch (Exception $ex) {
            DB::rollBack();
            // Manejar el error (opcional)
            error_log("Error al marcar notificaciones como leídas: " . $ex->getMessage());
        }
    
        // Simular un retraso para la recepción del cliente
        sleep(8); // Reducido a 2 segundos
    }

    public function create(Request $request, Response $response)
    {
        DB::beginTransaction(); // Iniciar transacción
        try {
            // Verificar o crear el sistema
            $system = System::firstOrCreate(
                ['name' => $request->name], // Condición para buscar
                ['name' => $request->name]  // Datos para crear si no existe
            );

            // Verificar o crear el rol
            $role = Role::firstOrCreate(
                ['name' => $request->rol], // Condición para buscar
                ['name' => $request->rol]  // Datos para crear si no existe
            );

            // Crear la notificación
            $notification = ModelsNotification::create([
                'message' => $request->message,
                'id_yoursytem' => $request->id_yoursytem,
            ]);

            // Crear la relación en users_read_notifications
            $userReadNotification = UserReadNotification::create([
                'id_user' => $request->id_user,
                'system_id' => $system->id,
                'roles_id' => $role->id,
                'notifications_id' => $notification->id,
            ]);

            DB::commit(); // Confirmar transacción

            return response()->json([
                'success' => true,
                'message' => 'Notificación creada correctamente',
                'data' => $userReadNotification,
            ], 201);

        } catch (Exception $ex) {
            DB::rollBack(); // Revertir transacción en caso de error
            return response()->json([
                'success' => false,
                'message' => 'Error al crear la notificación',
                'error' => $ex->getMessage(),
            ], 400);
        }
    }
    // public function stream(Request $request, Response $response)
    // {
    //     // Configurar los encabezados para la transmisión SSE
    //     header("Content-Type: text/event-stream");
    //     header("Cache-Control: no-cache");
    //     header("Connection: keep-alive");
    //     header("Access-Control-Allow-Origin: *"); 
    //     header("Access-Control-Allow-Methods: GET");  
    //     header("Access-Control-Allow-Headers: *"); 
    //     $notifications = DB::table('system')
    //     ->join('users_read_notifications', 'users_read_notifications.system_id', '=', 'system.id')
    //     ->join('roles', 'users_read_notifications.roles_id', '=', 'roles.id')
    //     ->join('notifications', 'users_read_notifications.notifications_id', '=', 'notifications.id')
    //     ->where('system.name', $request->name)
    //     ->where('roles.name', $request->rol)
    //     ->whereNotExists('users_read_notifications.id_user', $request->id_user)

    //     ->select(
    //         'system.name as system_name', 
    //         'roles.name as role_name',    
    //         'notifications.message',      
    //         'users_read_notifications.created_at as read_at'
    //     )
    //     ->get();
    //     // Ruta al archivo donde se guarda el mensaje

    //     // Verificar si el archivo existe
    //         // Leer el contenido del archivo

    //         // Enviar el mensaje como un evento SSE
    //         echo "event: message\n";
    //         echo "data: " . json_encode(['message' => $notifications]) . "\n\n";
       
    //         sleep(30);
    //     // Forzar que el contenido se envíe al cliente
    //     ob_flush();
    //     flush();

    //     // Opcional: Si deseas mantener la conexión abierta y enviar más mensajes más tarde,
    //     // puedes descomentar `sleep()` para simular un retraso en el servidor.
    //     // sleep(1);  // Simular un retraso para la recepción del cliente.
    // }
    // public function create(Request $request, Response $response)
    // {
    //     try {
    //         // Verificar o crear el sistema
    //         $system = System::firstOrCreate(
    //             ['name' => $request->name], // Condición para buscar
    //             ['name' => $request->name]  // Datos para crear si no existe
    //         );
    
    //         // Verificar o crear el rol
    //         $role = DB::table('roles')::firstOrCreate(
    //             ['name' => $request->rol], // Condición para buscar
    //             ['name' => $request->rol]  // Datos para crear si no existe
    //         );
    
    //         // Verificar o crear la notificación
    //         $notification = DB::table('notifications')::create([
    //             'message' => $request->message]

    //         );
    
    //         // Crear la notificación en la tabla `system`
    //         $systemNotification = DB::table('users_read_notifications')::create([
    //             'name' => $system->id,
    //             'rol_id' => $role->id, // Usar el ID del rol
    //             'notification_id' => $notification->id, // Usar el ID de la notificación
    //             'id_user' => $request->id_user,
    //         ]);
    
    //         return response()->json([
    //             'success' => true,
    //             'message' => 'Notificación creada correctamente',
    //             'data' => $systemNotification,
    //         ], 201);
    
    //     } catch (Exception $ex) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Error al crear la notificación',
    //             'error' => $ex->getMessage(),
    //         ], 400);
    //     }
    // }
}
