<?php

namespace App\Http\Controllers;
//-------------------------------------
use App\mailSendModel;
use App\Mail\email_bajas;
use Illuminate\Support\Facades\Mail;
use Validator;
//-------------------------------------
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
        $request->validate([
            "id" => "required",
            "motivo" => "required",
            "real_low_date" => "sometimes|date|nullable"
        ]);
        
        $tercero = new Terceros;
        $correo=mailSendModel::select('correo')->where("tcs_terceros_baja", "=", 1)->get()->toArray();
        if($tercero->bajaTercero($request->post()) === true) {

            $id=$request->post("id");
            $datos=$tercero->b_tercero($id);
            /* Aquí debe ir el envío del correo*/
             foreach ($correo as $key )
             {
                $obj_mail= new \stdClass();
                $obj_mail->data=$datos;
                $obj_mail->sender='SYSADMIN';
                $correo=Validator::make($key, ['correo' => 'regex:/^.+@(.+\..+)$/']);
                $mail = Mail::to(array($key["correo"]));
                //$mail=Mail::to("carlosazul5@hotmail.com");
                if (!$correo->fails() === true) 
                { 
                    $mail->send(new email_bajas($obj_mail));
                }
             }
            /* Fin del envío del correo*/
            return Response::json(true);
        }

        return Response::json(false);
    }
}
