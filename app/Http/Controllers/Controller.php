<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    public function json($m, $e)
    {
        return response()->json([
            "data" => $m,
            "error" => $e
        ]);
    }
    
    public function api_consulta_token()
    {
        return response()->json(["_token" => csrf_token()]);
    }

}
