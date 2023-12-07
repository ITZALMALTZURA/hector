<?php

namespace App\Http\Controllers;

use App\Models\Api;
use Illuminate\Http\Request;
use PhpParser\Node\Stmt\Return_;
use Illuminate\Support\Facades\DB;
use Stevebauman\Purify\Facades\Purify;
use Barryvdh\DomPDF\Facade\PDF;

class ApiController extends Controller
{

    //cuerpo del json
    public function json($m, $e)
    {
        return response()->json([
            "data" => $m,
            "error" => $e
        ]);
    }

    //retorno del token
    public function api_consulta_token()
    {
        return response()->json(["_token" => csrf_token()]);
    }

    //manipulacion del inicio de sesion y retorno de estatus de sesion
    public function sesion()
    {
        return Api::api_sesion_users();
    }

    //manipulacion de la peticion y retorno de datos de la peticion: usuarios activos
    public function api_consulta_users()
    {
        return Api::api_consulta_users();
        // return $this->json($msg,0);
    }

    //manipulacion de la peticion y retorno de datos de la peticion: usuario activo
    public function api_consulta_user($id)
    {
        return Api::api_consulta_user($id);
        // return $this->json($msg, 0);
    }

    //manipulacion de la peticion y retorno de datos de la peticion: nuevo usuario
    public function api_nuevo_user()
    {
        if (auth()->user()->permiso == "Administrador") {
            return Api::api_nuevo_user();
        } else {
            return $this->json("NIVEL DE PERMISO INVALIDO", 0);
        }
    }
    //manipulacion de la peticion y retorno de datos de la peticion: actualizar usuario
    public function api_actualizar_user(Request $request, $id)
    {

        if (auth()->user()->permiso == "Administrador") {
            return Api::api_actualizar_user($request, $id);
        } else if (auth()->user()->permiso == "Basico" && auth()->user()->id == $id) {
            return Api::api_actualizar_user($request, $id);
        } else {
            return $this->json("NIVEL DE PERMISO INVALIDO", 0);
        }
    }

    //manipulacion de la peticion y retorno de datos de la peticion: actualizar usuario
    public function api_eliminar_user($id)
    {

        if (auth()->user()->permiso == "Administrador") {
            return Api::api_eliminar_user($id);
        } else {
            return $this->json("NIVEL DE PERMISO INVALIDO", 0);
        }

        //return $this->json("NIVEL DE PERMISO INVALIDO", 0);
    }

    public function api_dowload_users()
    {
        $sql = Api::api_dowload_users();
        $pdf = PDF::loadView('pdf.users', compact('sql'));
        return $pdf->download('users.pdf');
        // return view('usuarios', compact('sql'));
    }
}
