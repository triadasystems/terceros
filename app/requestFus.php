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

        $descripcionB = "SE APLICO LA BAJA AUTOMÁTICA";
        $users_id = 1;

        if($forma == "manual") {
            $descripcionB = "SE APLICO LA BAJA";
            $users_id = Auth::user()->id;
            $noEmployee = Auth::user()->noEmployee;
        } 

        if($data["motivo"] == 0) {
            $tipoB = TipoBajas::select("id")->where("code", "=", 0)->first()->toArray();
            $motivo = $tipoB["id"];
        } else {
            $motivo = $data["motivo"];
        }

        $fus = new requestFus;
        $fus->id_generate_fus = strtotime(date("Y-m-d H:i:s"));
        $fus->type = $tipo;
        $fus->description = $descripcionB;
        $fus->users_id = $users_id;       
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
        'tcs_request_fus.low_date',
        DB::raw("CONCAT(tcs_external_employees.name,' ',tcs_external_employees.lastname1,' ',tcs_external_employees.lastname2) AS full_name"), 
        DB::raw("if(tcs_external_employees.badge_number IS NULL, 'S/N',tcs_external_employees.badge_number) AS gafete"),
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
        ->where('tcs_external_employees.status', '=','1')
        ->where("tcs_request_fus.low_date", "<", DB::raw("(SELECT CURDATE() + INTERVAL $dias DAY)"))
        ->where("tcs_request_fus.low_date", ">=", DB::raw("(SELECT CURDATE())"))
        ->get()
        ->toArray();
        return $fus; 
    }

}
