<?php

namespace App\Http\Controllers;
//-------------------------------------
use App\mailSendModel;
use App\Mail\email_f_ven;
use App\Mail\correo_fus_ven;
use App\Mail\correo_fus_ven_unico;
use Illuminate\Support\Facades\Mail;
use Validator;
//-------------------------------------
use App\setting;
use App\Terceros;
use App\requestFus;
use App\ActivedirectoryEmployees;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class NotificacionController extends Controller {
    
    public function not_caducidad() {
        ini_set('date.timezone','America/Mexico_City');
        $fecha_actual = date("Y-m-d");
        $a = new setting;
        $data = $a->recuperar_dias();
        $dia_limit = $data[0]['settings'];
        $b = new Terceros;
        $listado = $b->terceros_p_vencer($dia_limit);

        $correo = mailSendModel::select('correo')->where("tcs_terceros_baja", "=", 1)->get()->toArray();

        try {
            if(count($listado) > 0) {
                foreach ($correo as $key) {
                    $obj_mail = new \stdClass();
                    $obj_mail->data = $listado;
                    $obj_mail->sender ='SYSADMIN';
                    $correo = Validator::make($key, ['correo' => 'regex:/^.+@(.+\..+)$/']);
                    $mail = Mail::to(array($key["correo"]));
                    if (!$correo->fails() === true)
                    { 
                        $mail->send(new email_f_ven($obj_mail));
                    }
                }
            }
        } catch(Exception $e) {
            print_r($e);
        }
    }
    
    public function not_caducidad_auth_resp() {
        ini_set('date.timezone','America/Mexico_City');
        $fecha_actual = date("Y-m-d");
        
        $a = new setting;
        $data = $a->recuperar_dias();
        $dia_limit = $data[0]['settings'];

        // Proximos a ser dados de bajas
        $b = new Terceros;
        $getResponsables = $b->getResponsablesNotificacionBaja($dia_limit);
        
        try {
            if(count($getResponsables["mails"]) > 0) {
                foreach ($getResponsables["mails"] as $key) {
                    $listado = $b->terceros_p_vencer_byNumber($dia_limit, $key["number"]);
                    $obj_mail = new \stdClass();
                    $obj_mail->data = $listado;
                    $obj_mail->sender ='SYSADMIN';
                    $correo = Validator::make($key, ['email' => 'regex:/^.+@(.+\..+)$/']);
                    $mail = Mail::to(array($key["email"]));
                    if (!$correo->fails() === true) { 
                        $mail->send(new email_f_ven($obj_mail));
                    }
                }
            }
        } catch(Exception $e) {
            print_r($e);
        }
    }
    public function fus_vence()
    {
        ini_set('date.timezone','America/Mexico_City');
        $fecha_actual = date("Y-m-d");
        $a = new setting;
        $data = $a->recuperar_dias();
        $dia_limit = $data[0]['settings'];

        $a= new requestFus;
        $fuses=$a->fus_vence($dia_limit);
        
        foreach ($fuses as $value) {
            try {
                $correo = new ActivedirectoryEmployees;
                $dat=$correo->correo($value['id']);
                if (count($dat)>0)
                {
                    foreach ($dat as $send_meil) {
                        $obj_mail = new \stdClass();
                        $obj_mail->data = $value;
                        $obj_mail->sender ='SYSADMIN';
                        $correo = Validator::make($send_meil, ['correo' => 'regex:/^.+@(.+\..+)$/']);
                        $mail = Mail::to(array($send_meil["correo"]));
                        if (!$correo->fails() === true) { 
                            $mail->send(new correo_fus_ven_unico($obj_mail));
                        }
                    }
                }
            } catch(Exception $e) {
                print_r($e);
            }
        }
        //evia a los configurados
        $correo = mailSendModel::select('correo')->where("tcs_terceros_baja", "=", 1)->get()->toArray();
        try {
            if (count($fuses)>0)
            {
                foreach ($correo as $key) {
                    $obj_mail = new \stdClass();
                    $obj_mail->data = $fuses;
                    $obj_mail->sender ='SYSADMIN';
                    $correo = Validator::make($key, ['correo' => 'regex:/^.+@(.+\..+)$/']);
                    $mail = Mail::to(array($key["correo"]));
                    if (!$correo->fails() === true)
                    { 
                        $mail->send(new correo_fus_ven($obj_mail));
                    }
                }
            }
        } catch(Exception $e) {
            print_r($e);
        }
    }
}
