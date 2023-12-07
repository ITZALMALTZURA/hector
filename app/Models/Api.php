<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Stevebauman\Purify\Facades\Purify;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Request;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Api extends Model
{
    use HasFactory;

    static function json($m, $e)
    {
        return response()->json([
            "data" => $m,
            "error" => $e
        ]);
    }

    static function random_string()
    {
        $key = '';
        $keys = array_merge(range('a', 'z'), range(0, 9));

        for ($i = 0; $i < 10; $i++) {
            $key .= $keys[array_rand($keys)];
        }

        return $key;
    }


    //procesamiendo de datos de la peticion de usuarios activos
    static function api_consulta_users()
    {

        $sql = DB::table('users')
            ->select('nombre', 'apellido', 'telefono', 'email', 'fotografia', 'permiso', 'fecha_sesion', 'created_at', 'updated_at')
            ->where('estatus', 'ACTIVO')
            ->orderby('created_at')
            ->get();
        if (count($sql) > 0) {
            $msg = $sql;
        } else {
            $msg = "SIN USUARIOS ACTIVOS";
        }
        //$msg= Purify::clean($msg);
        return Api::json($msg, 0);
    }

    //procesamiendo de datos de la peticion de usuario activo
    static function api_consulta_user($id)
    {
        $id = Purify::clean($id);
        $sql = DB::table('users')
            ->select('nombre', 'apellido', 'telefono', 'email', 'fotografia', 'permiso', 'fecha_sesion', 'created_at', 'updated_at')
            ->where('estatus', 'ACTIVO')
            ->where('id', $id)
            ->orderby('created_at')
            ->get();
        if (count($sql) > 0) {
            $msg = $sql;
        } else {
            $msg = "SIN USUARIOS ACTIVOS";
        }
        return Api::json($msg, 0);
    }

    //actualizacion de la sesion del usuario
    static function api_sesion_users()
    {
        $now = Carbon::now();
        $sql = DB::table('users')
            ->where('id', auth()->user()->id)
            ->update(['estatus' => 'ACTIVO', 'fecha_sesion' => $now]);
        return Api::json("SESION_ACTIVA", 0);
    }
    //actualizacion de la sesion del usuario
    static function logout()
    {
        $now = Carbon::now();
        DB::table('users')
            ->where('id', auth()->user()->id)
            ->update(['estatus' => 'INACTIVO', 'fecha_sesion' => $now]);
        return Api::json("SESION_CERRADA", 0);
    }
    static function login()
    {
        $email = $_POST['email'];
        $sql = DB::table('users')
            ->where('email', $email)
            ->where('estatus', 'ELIMINADO')
            ->where('sesion', 'BLOQUEADA')
            ->count();
        if ($sql == 0) {
            return 0;
        } else {
            return 1;
        }
        //return Api::json("SESION_CERRADA", 0);
    }

    static function api_nuevo_user()
    {
        $messages = array();
        $e = 0;
        if (empty($_POST['nombre']) || !isset($_POST['nombre'])) {
            $messages['msg_nombre'] = "SE REQUIERE EL CAMPO NOMBRE";
            $e++;
        }
        if (empty($_POST['apellidos']) || !isset($_POST['apellidos'])) {
            $messages['msg_apellidos'] = "SE REQUIERE EL CAMPO APELLIDOS";
            $e++;
        }
        if (empty($_POST['telefono']) || !isset($_POST['telefono'])) {
            $messages['msg_telefono'] = "SE REQUIERE EL CAMPO TELEFONO";
            $e++;
        }
        if (empty($_POST['correo']) || !isset($_POST['correo'])) {
            $messages['msg_correo'] = "SE REQUIERE EL CAMPO CORREO";
            $e++;
        }
        if (empty($_POST['fotografia']) || !isset($_POST['fotografia'])) {
            $messages['msg_fotografia'] = "NO SE OPTUBO LA FOTOGRAFIA";
            $e++;
        }
        if (empty($_POST['password']) || !isset($_POST['password'])) {
            $messages['msg_password'] = "SE REQUIERE EL CAMPO PASSWORD";
            $e++;
        }
        if (empty($_POST['permiso']) || !isset($_POST['permiso'])) {
            $messages['msg_permiso'] = "SE REQUIER EL TIPO DE USUARIO";
            $e++;
        }
        $c = count($messages, COUNT_RECURSIVE);
        if ($c == 0) {


            $now = Carbon::now();
            $nombre = Purify::clean($_POST['nombre']);
            $apellidos = Purify::clean($_POST['apellidos']);
            $telefono = Purify::clean($_POST['telefono']);
            $correo = Purify::clean($_POST['correo']);
            $permiso = Purify::clean($_POST['permiso']);
            $tok = '7KbWdNn1fABmsqQu7V6MhK9om04FfqiLtUrDRse4a3IgBWSOuTGwvNycU0Tw';
            $pwd = bcrypt($_POST['password']);
            $ruta = $_POST['fotografia'];

            $ctelefono = (int)$telefono;

            if (strlen($telefono) < 10 || strlen($ctelefono) > 10) {
                $messages['msg_telefono'] = "TODOS LOS NUMEROS DE TELEFONO EN MEXICO TIENEN 10 DIGUITOS";
                $e++;
            }
            if (filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            } else {
                $messages['msg_correo'] = "FORMATO INVALIDO DE CORREO";
                $e++;
            }

            $c2 = count($messages, COUNT_RECURSIVE);
            $count = DB::table('users')
                ->where('telefono', '=', $telefono)
                ->count();
            if ($count > 0) {
                $messages['msg_duplicado'] = 'El NUMERO TELEFONICO ' . $telefono . ' YA EXISTE';
                $e++;
            }

            if ($c2 == 0 && $count == 0) {
                $image_location = 'users/img/' . Api::random_string() . '.jpg';
                $image_url = $ruta;
                $copy_image = copy($image_url, $image_location);

                if (file_exists($image_location)) {
                } else {
                    $messages['msg_fotografia'] = 'HA OCURRIDO UN ERROR CON:  ' . $image_location;
                    $e++;
                }
                $c3 = count($messages, COUNT_RECURSIVE);
                if ($c3 == 0) {
                    DB::table('users')
                        ->insert([
                            'nombre' => $nombre,
                            'apellido' => $apellidos,
                            'email' => $correo,
                            'password' => $pwd,
                            'remember_token' => $tok,
                            'permiso' => $permiso,
                            'fotografia' => $image_location,
                            'telefono' => $telefono,
                            'creo' => auth()->user()->nombre,
                            'estatus' => "ACTIVO",
                            'sesion' => "SIN LOGEAR",
                            'created_at' => $now,
                        ]);
                    //$ruta_foto->move('users/img/', $temp_name1);
                    $messages['msg_resgistrado'] = 'Registro Exitoso';
                    return Api::json($messages, $e);
                } else {
                    return Api::json($messages, $e);
                }
            } else {
                return Api::json($messages, $e);
            }
        } else {
            return Api::json($messages, $e);
        }
    }
    static function api_actualizar_user($request, $id)
    {
        $messages = array();
        $e = 0;


        if (empty($_POST['nombre']) || !isset($_POST['nombre'])) {
            $messages['msg_nombre'] = "SE REQUIERE EL CAMPO NOMBRE";
            $e++;
        }
        if (empty($_POST['apellidos']) || !isset($_POST['apellidos'])) {
            $messages['msg_apellidos'] = "SE REQUIERE EL CAMPO APELLIDOS";
            $e++;
        }
        if (empty($_POST['telefono']) || !isset($_POST['telefono'])) {
            $messages['msg_telefono'] = "SE REQUIERE EL CAMPO TELEFONO";
            $e++;
        }
        if (empty($_POST['correo']) || !isset($_POST['correo'])) {
            $messages['msg_correo'] = "SE REQUIERE EL CAMPO CORREO";
            $e++;
        }
        /*
        ///se descomenta al importar fotografia desde url
        
        if (empty($_POST['fotografia']) || !isset($_POST['fotografia'])) {
            $messages['msg_fotografia'] = "NO SE OPTUBO LA FOTOGRAFIA";
            $e++;
        }*/
        if (empty($_POST['password']) || !isset($_POST['password'])) {
            $messages['msg_password'] = "SE REQUIERE EL CAMPO PASSWORD";
            $e++;
        }
        if (empty($_POST['permiso']) || !isset($_POST['permiso'])) {
            $messages['msg_permiso'] = "SE REQUIER EL TIPO DE USUARIO";
            $e++;
        }
        $c = count($messages, COUNT_RECURSIVE);
        if ($c == 0) {


            $now = Carbon::now();
            $nombre = Purify::clean($_POST['nombre']);
            $apellidos = Purify::clean($_POST['apellidos']);
            $telefono = Purify::clean($_POST['telefono']);
            $correo = Purify::clean($_POST['correo']);
            $permiso = Purify::clean($_POST['permiso']);
            $tok = '7KbWdNn1fABmsqQu7V6MhK9om04FfqiLtUrDRse4a3IgBWSOuTGwvNycU0Tw';
            $pwd = bcrypt($_POST['password']);
            /*
           //se descomenta al importar fotografia desde url 
           $ruta = $_POST['fotografia'];*/

            $ctelefono = (int)$telefono;

            if (strlen($telefono) < 10 || strlen($ctelefono) > 10) {
                $messages['msg_telefono'] = "TODOS LOS NUMEROS DE TELEFONO EN MEXICO TIENEN 10 DIGUITOS";
                $e++;
            }
            if (filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            } else {
                $messages['msg_correo'] = "FORMATO INVALIDO DE CORREO";
                $e++;
            }

            $c2 = count($messages, COUNT_RECURSIVE);


            if ($c2 == 0) {
                //importacion de imagen solo con ruta
                /*$image = 'users/img/' . Api::random_string() . '.jpg';
                $image_location =  Api::random_string() . '.jpg';
                $image_url = $_POST['fotografia'];;
                copy($image_url, $image);

                if (file_exists($image)) {
                } else {
                    $messages['msg_fotografia'] = 'HA OCURRIDO UN ERROR CON:  ' . $image_url;
                    $e++;
                }*/
                //fin importacion de imagen solo con ruta

                //importacion de imagen tipo file

                $imagenOriginal = $request->file('fotografia'); //se obtiene la imagen
                $ruta = public_path() . 'users/img/'; // ruta de las imagenes guardadas

                if (empty($imagenOriginal)) {
                    $messages['msg_fotografia'] = 'HA OCURRIDO UN ERROR CON:  ' . $imagenOriginal;
                    $e++;
                } else {
                    $extension = $imagenOriginal->getClientOriginalExtension();
                    if ($extension == "jpg" || $extension == "JPG" || $extension == "png" || $extension == "jpeg" || $extension == "gif") {

                        $temp_name1 = Api::random_string() . '.' . $imagenOriginal->getClientOriginalExtension();

                        $request->file('fotografia')->move('users/img/', $temp_name1);

                        $image_location = $temp_name1;
                    } else {
                        $messages['msg_fotografia'] = 'HA OCURRIDO UN ERROR CON:  ' . $imagenOriginal;
                        $e++;
                    }
                }
                //fin importacion de imagen como file


                $c3 = count($messages, COUNT_RECURSIVE);
                if ($c3 == 0) {
                    $sql = DB::table('users')
                        ->where('id', $id)->first();
                    unlink('users/img/' . $sql->fotografia);


                    DB::table('users')
                        ->where('id', $id)
                        ->update([
                            'nombre' => $nombre,
                            'apellido' => $apellidos,
                            'email' => $correo,
                            'password' => $pwd,
                            'remember_token' => $tok,
                            'permiso' => $permiso,
                            'fotografia' => $image_location,
                            'telefono' => $telefono,
                            'creo' => auth()->user()->nombre,
                            'estatus' => "ACTIVO",
                            'sesion' => "SIN LOGEAR",
                            'updated_at' => $now,
                        ]);

                    $messages['msg_resgistrado'] = 'Registro Exitoso';
                    return Api::json($messages, $e);
                } else {
                    return Api::json($messages, $e);
                }
            } else {
                return Api::json($messages, $e);
            }
        } else {
            return Api::json($messages, $e);
        }
    }

    static function api_eliminar_user($id)
    {
        $now = Carbon::now();
        DB::table('users')
            ->where('id', $id)
            ->update([
                'creo' => auth()->user()->nombre,
                'estatus' => "ELIMINADO",
                'sesion' => "BLOQUEADA",
                'password' => "bloq",
                'updated_at' => $now,
            ]);

        return Api::json("Usuario Eliminado", 0);
    }


    static function api_dowload_users()
    {
        $sql = DB::table('users')
            ->select('nombre', 'apellido', 'telefono', 'email', 'fotografia', 'permiso', 'fecha_sesion', 'created_at', 'updated_at')
           
            ->orderby('created_at')
            ->get();

        return $sql;
    }
}
