<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Terceros;

//-------------------------------------
use App\mailSendModel;
use App\Mail\email_bajas;
use Illuminate\Support\Facades\Mail;
use Validator;
//-------------------------------------

class BajasautomaticasController extends Controller
{
    public function bajasAutomaticas() {
        $terceros = new Terceros;
        $listadoT = $terceros->listaBajaDiaria();
        $result = $terceros->bajasDiarias($listadoT);

        $correo = mailSendModel::select('correo')->where("tcs_terceros_baja", "=", 1)->get()->toArray();
        echo '<pre>';print_r($result);echo '</pre>';
    }
}
