<?php

namespace App\Http\Controllers;
//-------------------------------------
use App\mailSendModel;
use App\Mail\email_f_ven;
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

        $correo = mailSendModel::select('correo')->where("tcs_terceros_baja", "=", 1)->get()->toArray();

        try
        {
            if(count($listado) > 0)
            {
                foreach ($correo as $key)
                {
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
        }
        catch(Exception $e)
        {
            print_r($e);
        }
    }
    
}
