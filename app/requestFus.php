<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\TipoBajas;

class requestFus extends Model
{
    protected $table="tcs_request_fus";

    protected $fillable = [
        'id',
        'id_generate_fus',
        'description',
        'type',
        'created_at',
        'tcs_external_employees_id',
        'tcs_cat_helpdesk_id',
        'tcs_type_low_id',
        'tcs_number_responsable_authorizer',
        'real_low_date',
    ];

    protected $hidden = [];

    public $timestamps = false;

    public function altaFus($tipo, $data, $forma = "manual") {
        

        $noEmployee = null;
        $fus = new requestFus;

        if($forma == "manual") {
            $noEmployee = Auth::user()->noEmployee;
            $fus->description = "Se aplico la baja manual";
            $fus->users_id = Auth::user()->id;
        } 
        else{
            $fus->description = "Se aplico la baja automatica";
        }

        if($data["motivo"] == 0) {
            $tipoB = TipoBajas::select("id")->where("code", "=", 0)->first()->toArray();
            $motivo = $tipoB["id"];
        } else {
            $motivo = $data["motivo"];
        }

        
        $fus->id_generate_fus = strtotime(date("Y-m-d H:i:s"));
       
        $fus->type = $tipo;
        
        // if(Auth::user()->id) {
        //     $fus->users_id = Auth::user()->id; // aqui crasheo la subrutina y no inserta en la tabla de FUS
        // }
        
        $fus->tcs_type_low_id = $motivo;
        $fus->tcs_external_employees_id = $data["id"];
        $fus->real_low_date = $data["real_low_date"];
        $fus->tcs_number_responsable_authorizer = $noEmployee;
        
        if ($fus->save()) {
            return $fus->id;
        }

        return false;
    }
    public function fus_vence($dias)
    {
        $fus= requestFus::select('tcs_request_fus.id',
        'tcs_request_fus.id_generate_fus',
        'tcs_request_fus.description',
        'tcs_request_fus.fus_physical',
        DB::raw("(SELECT
            GROUP_CONCAT(name)
                FROM
                    applications
                INNER JOIN
                    tcs_applications_employee
                ON
                    tcs_applications_employee.applications_id = applications.id
                WHERE
            tcs_applications_employee.tcs_request_fus_id = tcs_request_fus.id) AS app"),
        DB::raw("(SELECT
        GROUP_CONCAT(name,if(type=1,' /Autorizador',' /Responsable'))
            FROM
                tcs_autorizador_responsable
            WHERE
                tcs_autorizador_responsable.tcs_request_fus_id = tcs_request_fus.id
                AND tcs_autorizador_responsable.status=1) AS aut_res")
        )
        ->join('tcs_external_employees','tcs_request_fus.tcs_external_employees_id','=','tcs_external_employees.id')
        ->where('tcs_request_fus.type', '!=', '3')
        ->where('tcs_request_fus.status_fus', '=','1')
        ->where("tcs_request_fus.low_date", "<", DB::raw("(SELECT CURDATE() + INTERVAL $dias DAY)"))
        ->get()
        ->toArray();
        return $fus; 
    }

}
