<?php

namespace App\Http\Controllers;
//-------------------------------------
use App\mailSendModel;
use App\Mail\email_bajas;
use Illuminate\Support\Facades\Mail;
use Validator;
//-------------------------------------
use App\setting;
use App\Terceros;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class NotificacionController extends Controller
{
    public function index()
    {

    }
    public function not_caducidad()
    {
        ini_set('date.timezone','America/Mexico_City');
        $fecha_actual = date("Y-m-d");
        $a = new setting;
        $data = $a->recuperar_dias();
        $dia_limit = $data[0]['settings'];
        $b = new Terceros;
        $listado = $b->terceros_p_vencer($dia_limit);
        echo "los dias son: "; print_r($listado);
    }
    
}
