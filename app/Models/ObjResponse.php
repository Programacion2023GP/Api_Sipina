<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ObjResponse extends Model
{
    public static function CorrectResponse() {
        return [
            "status_code" => 200,
            "status" => true,
            "message" => "Petición exitosa.",
            "alert_icon" => "success",
            "alert_title" => "Éxito!",
            "alert_text" => "",
            "result" => [],
            "toast" => true,
        ];
    }

    public static function DefaultResponse() {
        return [
            "status_code" => 500,
            "status" => false,
            "message" => "No se pudo completar la petición.",
            "alert_icon" => "informative",
            "alert_title" => "Lo sentimos.",
            "alert_text" => "Hubo un problema con el servidor. Inténtelo más tarde.",
            "result" => [],
            "toast" => false,
        ];
    }

    public static function CatchResponse($message) {
        return [
            "status_code" => 400,
            "status" => false,
            "message" => $message,
            "alert_icon" => "error",
            "alert_title" => "Oops!",
            "alert_text" => "Algo salió mal. Verifique sus datos.",
            "result" => [],
            "toast" => false,
        ];
    }
}
