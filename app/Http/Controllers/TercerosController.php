<?php

namespace App\Http\Controllers;

use App\Terceros;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;

class TercerosController extends Controller
{
    public function index() {
        return view('bajas.lista');
    }

    public function tercerosAsignados(Terceros $terceros, Request $request) {
        return Datatables::of($terceros->tercerosAsignados($request->post('noEmployee')))->make(true);
    }
}
