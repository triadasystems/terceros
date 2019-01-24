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

        /* Aquí debe ir el envío del correo*/
        $correo = mailSendModel::select('correo')->where("tcs_terceros_baja", "=", 1)->get()->toArray();
        $idTerceros = explode(",", $result);
        
        $listaTercerosBajas = new Terceros;
        $datos = $listaTercerosBajas->b_tercero_automaticas($idTerceros);
        
        foreach ($correo as $key ) {
            $obj_mail = new \stdClass();
            $obj_mail->data = $datos;
            $obj_mail->sender ='SYSADMIN';
            $correo = Validator::make($key, ['correo' => 'regex:/^.+@(.+\..+)$/']);
            $mail = Mail::to(array($key["correo"]));
            if (!$correo->fails() === true) { 
                $mail->send(new email_bajas($obj_mail));
            }
        }
        /* Fin del envío del correo*/
    }
}
