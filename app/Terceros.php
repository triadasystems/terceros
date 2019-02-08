<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\requestFus;
use App\HistoricoTerceros;
use App\ApplicationsEmployee;
use App\ActivedirectoryEmployees;
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

    public $numberEmployee;

    public $timestamps = false;
    
    public function tercerosAsignados($noBadge = null) {
        return Terceros::select(
            'id',
            'id_external',
            'name',
            'lastname1',
            'lastname2',
            DB::raw("DATE_FORMAT(initial_date, '%d-%m-%Y') AS initial_date"),
            DB::raw("DATE_FORMAT(low_date, '%d-%m-%Y') AS low_date"),
            'badge_number',
            'email',
            'created_at',
            'status'
        )
        ->where("status", "=", 1)
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
            "authorizing_name" => "PENDIENTE",
            "authorizing_number" => 123,
            "responsible_name" => "PENDIENTE",
            "responsible_number" => 123,
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
        ->where("tcs_external_employees.id", "=", $data)
        ->where("tcs_request_fus.type", "=", "3")
        ->get()->toArray();
        
        return $tercero;
    }

    public function listaBajaDiaria() {
        return Terceros::where("low_date", "<", DB::raw("CURDATE()"))->where("status", "=", 1)->get()->toArray();
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

    public static function b_tercero_automaticas($data)
    {
        return Terceros::select(
            "tcs_external_employees.id_external as id_externo" ,
            "tcs_external_employees.name as nombre",
            "tcs_external_employees.lastname1 as a_paterno",
            "tcs_external_employees.lastname2 as a_materno",
            "tcs_request_fus.id_generate_fus as fus", 
            "tcs_cat_suppliers.name as empresa", 
            "tcs_request_fus.created_at as fecha_baja",
            "tcs_type_low.type"
        )
        ->join("tcs_request_fus","tcs_external_employees.id","=","tcs_request_fus.tcs_external_employees_id")
        ->join("tcs_cat_suppliers","tcs_external_employees.tcs_externo_proveedor","=","tcs_cat_suppliers.id")
        ->join("tcs_type_low","tcs_request_fus.tcs_type_low_id","=","tcs_type_low.id")
        ->whereIn("tcs_external_employees.id", $data)
        ->where("tcs_request_fus.type", "=", "3")
        ->get()->toArray();
    }

    public function terceros_p_vencer($dias) {
        $tercero= Terceros::select(
            "tcs_external_employees.id_external AS emp_keyemp", 
            DB::raw("DATEDIFF(tcs_external_employees.low_date, CURDATE()) AS d_dif"), 
            DB::raw("CONCAT(tcs_external_employees.name,' ',tcs_external_employees.lastname1,' ',tcs_external_employees.lastname2) AS full_name"), 
            DB::raw("if(tcs_external_employees.badge_number IS NULL, 'S/N',tcs_external_employees.badge_number) AS gafete"), 
            "tcs_external_employees.authorizing_name AS autorizador", 
            "tcs_external_employees.responsible_name AS responsable",
            "tcs_cat_suppliers.name AS empresa"
        )
        ->join('tcs_cat_suppliers','tcs_external_employees.tcs_externo_proveedor','=','tcs_cat_suppliers.id')
        ->where('tcs_external_employees.status','=','1')
        ->where('tcs_external_employees.low_date','<=',DB::raw("(SELECT CURDATE() + INTERVAL $dias DAY)"))->get()->toArray();
        return $tercero;
    }

    public function terceros_p_vencer_byNumber($dias, $number) {
        $this->numberEmployee = $number;
        
        $tercero = Terceros::select(
            "tcs_external_employees.id_external AS emp_keyemp", 
            DB::raw("DATEDIFF(tcs_external_employees.low_date, CURDATE()) AS d_dif"), 
            DB::raw("CONCAT(tcs_external_employees.name,' ',tcs_external_employees.lastname1,' ',tcs_external_employees.lastname2) AS full_name"), 
            DB::raw("if(tcs_external_employees.badge_number IS NULL, 'S/N',tcs_external_employees.badge_number) AS gafete"), 
            "tcs_external_employees.authorizing_name AS autorizador", 
            "tcs_external_employees.responsible_name AS responsable",
            "tcs_cat_suppliers.name AS empresa"
        )
        ->join('tcs_cat_suppliers','tcs_external_employees.tcs_externo_proveedor','=','tcs_cat_suppliers.id')
        ->where('tcs_external_employees.status','=','1')
        ->where('tcs_external_employees.low_date','<=',DB::raw("(SELECT CURDATE() + INTERVAL $dias DAY)"))
        ->where(function($condition){
            $condition->where("tcs_external_employees.authorizing_number", "=", $this->numberEmployee)
                ->orWhere("tcs_external_employees.responsible_number", "=", $this->numberEmployee);
        })
        ->get()
        ->toArray();

        return $tercero;
    }

    public function getResponsablesNotificacionBaja($dias) {
        $tercero = Terceros::select(
            "tcs_external_employees.authorizing_number", 
            "tcs_external_employees.responsible_number"
        )
        ->join('tcs_cat_suppliers','tcs_external_employees.tcs_externo_proveedor','=','tcs_cat_suppliers.id')
        ->where('tcs_external_employees.status','=','1')
        ->where('tcs_external_employees.low_date','<=',DB::raw("(SELECT CURDATE() + INTERVAL $dias DAY)"))->get()->toArray();
        
        $responsables = array();

        foreach($tercero as $index => $row) {
            if (!in_array($row["authorizing_number"], $responsables)) {
                $responsables[] = $row["authorizing_number"];
            }
            if (!in_array($row["responsible_number"], $responsables)) {
                $responsables[] = $row["responsible_number"];
            }
        }

        $correo = new ActivedirectoryEmployees;

        $response = array();
        $response["numbers"] = $responsables;
        $response["mails"] = $correo->getmails($responsables);

        return $response;
    }
}