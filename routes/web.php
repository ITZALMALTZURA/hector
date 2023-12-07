<?php

use App\Models\Api;
use App\Models\Permiso;
use Barryvdh\DomPDF\Facade\PDF;
use Stevebauman\Purify\Purify;
use PhpParser\Node\Stmt\Return_;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Redirect;
use OwenIt\Auditing\Contracts\Auditable;
use App\Http\Controllers\Auth\LoginController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::group(['middleware' => 'auth'], function () {

    Route::get('/', 'App\Http\Controllers\ApiController@sesion'); //rutas por defecto de laravel
    Route::get('home', 'App\Http\Controllers\ApiController@sesion'); //rutas por defecto de laravel
    Route::get('welcome', 'App\Http\Controllers\ApiController@sesion'); //rutas por defecto de laravel

    Auth::routes();
    Route::get('consulta_users', 'App\Http\Controllers\ApiController@api_consulta_users'); //peticion para consulta de usuarios activos
    Route::get('consulta_user/{id}', 'App\Http\Controllers\ApiController@api_consulta_user'); //peticion para consulta de usuario activo
    Route::post('nuevo_user', 'App\Http\Controllers\ApiController@api_nuevo_user'); //peticion para consulta crear nuevo usuario
    Route::put('actualizar_user/{id}', 'App\Http\Controllers\ApiController@api_actualizar_user'); //peticion para actualizar un usuario
    Route::delete('eliminar_user/{id}', 'App\Http\Controllers\ApiController@api_eliminar_user'); //peticion para eliminar un usuario activo
   // Route::get('dowload_users', 'App\Http\Controllers\ApiController@api_dowload_users'); //peticion para eliminar un usuario activo

 

    Route::get('dowload_users', function () {
        $sql = Api::api_dowload_users();
        $pdf = PDF::loadView('pdf_users', compact('sql'));
        return $pdf->download('users.pdf');
        //return view('pdf_users', compact('sql'));
    });
      
});



Route::get('token', 'App\Http\Controllers\ApiController@api_consulta_token'); //peticion del token
Route::get('login', 'App\Http\Controllers\Auth\LoginController@showLoginForm')->name('login'); //peticion de vista del login, para pruebas
Route::post('login', 'App\Http\Controllers\Auth\LoginController@login'); //peticion de login

Route::get('logout', function () {
    return Api::logout();
    Auth::logout();
    //return Redirect::to('login');
});
