<?php

namespace App\Http\Controllers;

use App\Models\childrens;
use App\Models\ObjResponse;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ControllerChildrens extends Controller
{
    public function created(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string',
                'lastName' => 'required|string',
                'secondSurname' => 'required|string',
                'age' => 'required|integer',
                'birtDate' => 'required|date',
                'Rfc' => 'required|string',
                'placeWas' => 'required|string',
                'typeWork' => 'required|string',
                'initialSchedule' => 'required|string',
                'finalSchedule' => 'required|string',
                'tutor' => 'required|string',
                'conditions' => 'required|string',
                'observations' => 'required|string',

            ]);
            $childrens = childrens::create([
                'name' => $request->name,
                'lastName' => $request->lastName,
                'secondSurname' => $request->secondSurname,
                'age' => $request->age,
                'birtDate' => $request->birtDate,
                'Rfc' => $request->Rfc,
                'placeWas' => $request->placeWas,
                'typeWork' => $request->typeWork,
                'initialSchedule' => $request->initialSchedule,
                'finalSchedule' => $request->finalSchedule,
                'tutor' => $request->tutor,
                'conditions' => $request->conditions,
                'observations' => $request->observations,
                'users_id' => Auth::user()->id,
            ]);

            // Generar token de acceso

            return response()->json(ObjResponse::CorrectResponse() + ['message' => 'creada exitosamente'], 200);
        } catch (\Exception $e) {
            error_log('Error al registrar usuario: ' . $e->getMessage());
            return response()->json(ObjResponse::CatchResponse('Ocurrió un error al registrar el usuario.'), 400);
        }
    }
    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'name' => 'required|string',
                'lastName' => 'required|string',
                'secondSurname' => 'required|string',
                'age' => 'required|integer',
                'birtDate' => 'required|date',
                'placeWas' => 'required|string',
                'Rfc' => 'required|string',
                'typeWork' => 'required|string',
                'initialSchedule' => 'required|string',
                'finalSchedule' => 'required|string',
                'tutor' => 'required|string',
                // 'conditions' => 'required|string',
                // 'observations' => 'required|string',

            ]);
            $children = Childrens::findOrFail($id);
            function formatDate($date, $fromFormat, $toFormat)
            {
                $dateTime = DateTime::createFromFormat($fromFormat, $date);
                return $dateTime ? $dateTime->format($toFormat) : null;
            }
            $children->name = $request->name;
            $children->lastName = $request->lastName;
            $children->secondSurname = $request->secondSurname;
            $children->age = $request->age;
            $children->placeWas = $request->placeWas;
            $children->Rfc = $request->Rfc;
            $children->typeWork = $request->typeWork;
            $children->tutor = $request->tutor;
            $children->conditions = $request->conditions;
            $children->observations = $request->observations;
            $children->users_id = Auth::user()->id;

            // Formatear y asignar birtDate si viene en formato 'd/m/Y' o 'Y-m-d'
            if (DateTime::createFromFormat('d/m/Y', $request->birtDate) !== false) {
                $children->birtDate = formatDate($request->birtDate, 'd/m/Y', 'Y-m-d');
            } elseif (DateTime::createFromFormat('Y-m-d', $request->birtDate) !== false) {
                $children->birtDate = $request->birtDate;
            } else {
                // Manejar error o asignar un valor por defecto si el formato no es válido
                $children->birtDate = null; // O asignar un valor por defecto adecuado
            }

            // Formatear y asignar initialSchedule si viene en formato 'H:i' o 'H:i:s'
            if (DateTime::createFromFormat('H:i', $request->initialSchedule) !== false) {
                $children->initialSchedule = formatDate($request->initialSchedule, 'H:i', 'H:i:s');
            } elseif (DateTime::createFromFormat('H:i:s', $request->initialSchedule) !== false) {
                $children->initialSchedule = $request->initialSchedule;
            } else {
                // Manejar error o asignar un valor por defecto si el formato no es válido
                $children->initialSchedule = null; // O asignar un valor por defecto adecuado
            }

            // Formatear y asignar finalSchedule si viene en formato 'H:i' o 'H:i:s'
            if (DateTime::createFromFormat('H:i', $request->finalSchedule) !== false) {
                $children->finalSchedule = formatDate($request->finalSchedule, 'H:i', 'H:i:s');
            } elseif (DateTime::createFromFormat('H:i:s', $request->finalSchedule) !== false) {
                $children->finalSchedule = $request->finalSchedule;
            } else {
                // Manejar error o asignar un valor por defecto si el formato no es válido
                $children->finalSchedule = null; // O asignar un valor por defecto adecuado
            }

            // Guardar los cambios en la base de datos
            $children->save();



            // Generar token de acceso

            return response()->json(ObjResponse::CorrectResponse() + ['message' => 'actualizado exitosamente'], 200);
        } catch (\Exception $e) {
            error_log('Error al registrar usuario: ' . $e->getMessage());
            return response()->json(ObjResponse::CatchResponse('Ocurrió un error al registrar el usuario.'), 400);
        }
    }
    public   function formatDate($date, $fromFormat, $toFormat)
    {
        $dateTime = DateTime::createFromFormat($fromFormat, $date);
        return $dateTime ? $dateTime->format($toFormat) : null;
    }
    public function index(Request $request)
    {
        try {
            setlocale(LC_TIME, 'es_ES'); // set locale to Spanish



            $childrens = childrens::join('users', 'childrens.users_id', '=', 'users.id')
                ->leftJoin('institutions', 'users.institution_id', '=', 'institutions.id')
                ->select(
                    'childrens.*',
                    DB::raw('institutions.name as institution_name'),
                    DB::raw('CONCAT(users.name, " ", users.lastName, " ", users.secondSurname) as user_full_name')
                )
                ->where('childrens.active', 1);

            // Verificamos que Auth::user() no sea null antes de acceder a sus propiedades
            if (Auth::check()) {
                $user = Auth::user();

                if ($user->typeUser == 4) {
                    $childrens = $childrens->where('childrens.users_id', $user->id);
                }

                if ($user->typeUser == 3) {
                    $childrens = $childrens->where('users.institution_id', $user->institution_id);
                }
            }

            $childrens = $childrens->get();


            foreach ($childrens as $child) {
                $birtDate = strtotime($child->birtDate);
                $month = strftime('%B', $birtDate);
                $child->birtDate = date('d/m/Y', $birtDate); // Formato "22/09/2000"

                $initialSchedule = strtotime($child->initialSchedule);
                $child->initialSchedule = strftime('%I:%M %p', $initialSchedule); // 3:19 pm

                $finalSchedule = strtotime($child->finalSchedule);
                $child->finalSchedule = strftime('%I:%M %p', $finalSchedule); // 7:19 pm
            }
            return response()->json(ObjResponse::CorrectResponse() + ['data' => $childrens], 200);
        } catch (\Exception $e) {
            error_log('Ocurrió un error: ' . $e->getMessage());
            return response()->json(ObjResponse::CatchResponse('Ocurrió un error.'), 400);
        }
    }
    public function charts(Request $request)
    {
        try {
            setlocale(LC_TIME, 'es_ES'); // set locale to Spanish


            $childrens = DB::table('childrens')
                ->join('users', 'childrens.users_id', '=', 'users.id')
                ->leftJoin('institutions', 'users.institution_id', '=', 'institutions.id')
                ->select(
                    DB::raw('CONCAT(childrens.name, " ", childrens.lastName, " ", childrens.secondSurname) as name'),
                    'childrens.age',
                    'childrens.birtDate',
                    'childrens.placeWas',
                    'childrens.typeWork',
                    'childrens.initialSchedule',
                    'childrens.finalSchedule',
                    'childrens.tutor',
                    'childrens.conditions',
                    'childrens.observations',
                    'childrens.Rfc',

                    'institutions.name as institucion'
                )
                ->where('childrens.active', 1);

            if (Auth::check()) {
                $user = Auth::user();

                if ($user->typeUser == 4) {
                    $childrens = $childrens->where('childrens.users_id', $user->id);
                }

                if ($user->typeUser == 3) {
                    $childrens = $childrens->where('users.institution_id', $user->institution_id);
                }
            }
            $childrens = $childrens->get();



            foreach ($childrens as $child) {
                $birtDate = strtotime($child->birtDate);
                $month = strftime('%B', $birtDate);
                $child->birtDate = date('d/m/Y', $birtDate); // Formato "22/09/2000"

                $initialSchedule = strtotime($child->initialSchedule);
                $child->initialSchedule = strftime('%I:%M %p', $initialSchedule); // 3:19 pm

                $finalSchedule = strtotime($child->finalSchedule);
                $child->finalSchedule = strftime('%I:%M %p', $finalSchedule); // 7:19 pm
            }
            return response()->json(ObjResponse::CorrectResponse() + ['data' => $childrens], 200);
        } catch (\Exception $e) {
            error_log('Ocurrió un error: ' . $e->getMessage());
            return response()->json(ObjResponse::CatchResponse('Ocurrió un error.'), 400);
        }
    }
    public function destroy(Request $request, int $id)
    {
        try {
            $childrens = childrens::find($id);
            $childrens->active = 0;
            $childrens->save();
            return response()->json(ObjResponse::CorrectResponse() + ['message' => 'eliminado correctamente'], 200);
        } catch (\Exception $e) {
            error_log('Ocurrió un error: ' . $e->getMessage());
            return response()->json(ObjResponse::CatchResponse('Ocurrió un error.'), 400);
        }
    }
    public function Rfc(Request $request)
    {
        try {
            $childrens = Childrens::join('users', 'childrens.users_id', '=', 'users.id')
                ->select('childrens.Rfc', DB::raw('count(childrens.Rfc) as total'))
                ->where('childrens.active', 1);

            if (Auth::check()) {
                $user = Auth::user();
                if ($user->typeUser == 4) {
                    $childrens = $childrens->where('childrens.users_id', $user->id);
                }

                if ($user->typeUser == 3) {
                    $childrens = $childrens->where('users.institution_id', $user->institution_id);
                }
            }

            $childrens = $childrens->groupBy('childrens.Rfc')->get();

            return response()->json(ObjResponse::CorrectResponse() + ['data' => $childrens], 200);
        } catch (\Exception $e) {
            error_log('Ocurrió un error: ' . $e->getMessage());
            return response()->json(ObjResponse::CatchResponse('Ocurrió un error.'), 400);
        }
    }

    public function RfcExist(Request $request, string $rfc)
    {
        try {
            $childrens = Childrens::where('Rfc', $rfc)->get();

            // if ($childrens->isEmpty()) {
            //     throw new \Exception('El RFC no existe.');
            // }

            return response()->json(ObjResponse::CorrectResponse() + ['data' => $childrens], 200);
        } catch (\Exception $e) {
            error_log('Ocurrió un error: ' . $e->getMessage());
            return response()->json(ObjResponse::CatchResponse($e->getMessage()), 400);
        }
    }
}
