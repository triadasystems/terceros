<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\requestFus;
use App\HistoricoTerceros;
use App\ApplicationsEmployee;
use Illuminate\Support\Facades\DB;

class Terceros extends Model
{
    protected $table="tcs_external_employees";

    protected $fillable = [
        'id',
        'id_external',
        'name',
        'lastname1',
        'lastname2',
        'initial_date',
        'low_date',
        'badge_number',
        'email',
        'authorizing_name',
        'authorizing_number',
        'responsible_name',
        'responsible_number',
        'created_at',
        'status',
    ];

    protected $hidden = [
        'tcs_subfijo_id', 'tcs_externo_proveedor',
    ];

    public $timestamps = false;
    
    public function tercerosAsignados($noBadge = null) {
        return Terceros::where("authorizing_number", "=", $noBadge)
                        ->where("status", "=", "1")
                        ->orWhere("responsible_number", "=", $noBadge)
                        ->where("status", "=", "1")
                        ->get()
                        ->toArray();
    }

    public function bajaTercero($data, $forma = "manual") {
        $tercero = Terceros::find($data["id"]);

        $applicationsEmployee = new ApplicationsEmployee;
        $aplicacionesDelTercero = "";
        
        
        foreach($applicationsEmployee->applicationEmployeeById($tercero->id) as $row) {
            $aplicacionesDelTercero .= $row["applications_id"].",";
        }

        $dataHistorico = array(
            "id_external" => $tercero->id_external,
            "name" => $tercero->name,
            "lastname1" => $tercero->lastname1,
            "lastname2" => $tercero->lastname2,
            "initial_date" => $tercero->initial_date,
            "low_date" => $tercero->low_date,
            "badge_number" => $tercero->badge_number,
            "email" => $tercero->email,
            "authorizing_name" => $tercero->authorizing_name,
            "authorizing_number" => $tercero->authorizing_number,
            "responsible_name" => $tercero->responsible_name,
            "responsible_number" => $tercero->responsible_number,
            "created_at" => $tercero->created_at,
            "status" => $tercero->status,
            "tcs_fus_ext_hist" => $tercero->tcs_fus_ext_hist,
            "tcs_applications_ids" => substr($aplicacionesDelTercero, 0, -1),
            "tcs_subfijo_id" => $tercero->tcs_subfijo_id,
            "tcs_externo_proveedor" => $tercero->tcs_externo_proveedor
        );

        $tercero->status = 2;
        
        if($tercero->save()) {
            $fus = new requestFus;
            $id = $fus->altaFus(3, $data, $forma);

            if($id !== false) {
                $historicoTercero = new HistoricoTerceros;
                $historicoTercero->altaHistoricoTercero($dataHistorico, $id);
            }

            return true;
        }
        return false;
    }
    public static function b_tercero($data)
    {
        $tercero = Terceros::select("tcs_external_employees.id_external as id_externo" ,"tcs_external_employees.name as nombre","tcs_external_employees.lastname1 as a_paterno","tcs_external_employees.lastname2 as a_materno",
        "tcs_request_fus.id_generate_fus as fus", "tcs_cat_suppliers.name as empresa", "tcs_request_fus.created_at as fecha_baja","tcs_type_low.type")
        ->join("tcs_request_fus","tcs_external_employees.id","=","tcs_request_fus.tcs_external_employees_id")
        ->join("tcs_cat_suppliers","tcs_external_employees.tcs_externo_proveedor","=","tcs_cat_suppliers.id")
        ->join("tcs_type_low","tcs_request_fus.tcs_type_low_id","=","tcs_type_low.id")
            ->where("tcs_external_employees.id", "=", $data)->get()->toArray();
        return $tercero;
    }

    public function listaBajaDiaria() {
        return Terceros::where("low_date", "=", DB::raw("CURDATE()"))->where("status", "=", 1)->get()->toArray();
    }

    public function bajasDiarias($terceros) {
        $tercero = new Terceros;

        $response = null;

        foreach($terceros as $row) {
            $data = array();
            $data["motivo"] = 0;
            $data["id"] = $row["id"];
            $data["real_low_date"] = null;

            if($tercero->bajaTercero($data, "automatica") === true) {
                $response .= $row["id"].",";
            }
        }

        return substr($response, 0, -1);
    }
}