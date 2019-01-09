<?php

namespace App\Http\Controllers;

use App\Terceros;
use App\TipoBajas;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\Response;

class TercerosController extends Controller
{
    public function index(TipoBajas $TipoBajas) {
        $tiposB = $TipoBajas->tiposBajas();
        return view('bajas.lista')->with(["tiposBajas" => $tiposB]);
    }

    public function tercerosAsignados(Terceros $terceros, Request $request) {
        return Datatables::of($terceros->tercerosAsignados($request->post('noEmployee')))->make(true);
    }

    public function bajatercero(Request $request) {
        $id = $request->post("id");
        $estado = $request->post("estado");

        $request->validate([
            "id" => ["required"],
            "motivo" => ["required"]   
        ]);
        
        $tercero = new Terceros;

        if($tercero->bajaTercero($id) === true) {
            return Response::json(true);
        }

        return Response::json(false);
    }
}
