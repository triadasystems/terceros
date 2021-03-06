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

        $response = array();

        // foreach($activos as $key => $value) {
        $auto_resp = requestFus::select(
            'name',
            'number',
            'tcs_autorizador_responsable.type AS tipo'
        )
        ->join(
            "tcs_autorizador_responsable",
            "tcs_autorizador_responsable.tcs_request_fus_id", 
            "=",
            "tcs_request_fus.id"
        )
        ->where("tcs_external_employees_id", "=", $data["id"])
        ->where("tcs_request_fus.type", "=", 1)
        ->where("tcs_autorizador_responsable.status", "=", 1)
        ->distinct()
        ->get()
        ->toArray();

        $autorizador = '';
        $responsable = '';

        foreach($auto_resp as $ind => $val) {
            switch ($val["tipo"]) {
                case 1:
                    if(!isset($autorizador)) {
                        $autorizador = $val["name"]." | ".$val["number"].",";
                    } else {
                        $autorizador .= $val["name"]." | ".$val["number"].",";
                    }
                
                    break;    
                case 2:
                    if(!isset($responsable)) {
                        $responsable = $val["name"]." | ".$val["number"].",";
                    } else {
                        $responsable .= $val["name"]." | ".$val["number"].",";
                    }
                    
                    break;
            }
        }
        
        $response["autorizador"] = substr($autorizador, 0, -1);
        $response["responsable"] = substr($responsable, 0, -1);
        
        // }

        $dataHistorico = array(
            "id_external" => $tercero->id_external,
            "name" => $tercero->name,
            "lastname1" => $tercero->lastname1,
            "lastname2" => $tercero->lastname2,
            "initial_date" => $tercero->initial_date,
            "low_date" => $tercero->low_date,
            "badge_number" => $tercero->badge_number,
            "email" => $tercero->email,
            "authorizing_name" => $response["autorizador"],
            "authorizing_number" => null,
            "responsible_name" => $response["responsable"],
            "responsible_number" => null,
            "created_at" => $tercero->created_at,
            "status" => 2,
            "tcs_fus_ext_hist" => $tercero->tcs_fus_ext_hist,
            "tcs_applications_ids" => substr($aplicacionesDelTercero, 0, -1),
            "tcs_subfijo_id" => $tercero->tcs_subfijo_id,
            "tcs_externo_proveedor" => $tercero->tcs_externo_proveedor
        );

        $tercero->status = 2;

        if($tercero->save()) {
            $fus = new requestFus;

            $tipoFus = 4;

            if($forma == "manual") {
                $tipoFus = 3;
            }

            $id = $fus->altaFus($tipoFus, $data, $forma);

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
        //return Terceros::where("low_date", "<", DB::raw("CURDATE()"))->where("status", "=", 1)->get()->toArray();
        return Terceros::where("low_date", "<", "2019-03-02")->where("status", "=", 1)->get()->toArray();
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
            "tcs_external_employees.id AS id",
            "tcs_external_employees.id_external AS emp_keyemp", 
            DB::raw("DATEDIFF(tcs_external_employees.low_date, CURDATE()) AS d_dif"), 
            DB::raw("CONCAT(tcs_external_employees.name,' ',tcs_external_employees.lastname1,' ',tcs_external_employees.lastname2) AS full_name"), 
            DB::raw("if(tcs_external_employees.badge_number IS NULL, 'S/N',tcs_external_employees.badge_number) AS gafete"), 
            // "tcs_external_employees.authorizing_name AS autorizador", 
            // "tcs_external_employees.responsible_name AS responsable",
            "tcs_cat_suppliers.name AS empresa"
        )
        ->join('tcs_cat_suppliers','tcs_external_employees.tcs_externo_proveedor','=','tcs_cat_suppliers.id')
        ->where('tcs_external_employees.status','=','1')
        ->where('tcs_external_employees.low_date','<=',DB::raw("(SELECT CURDATE() + INTERVAL $dias DAY)"))
        // ->where(function($condition){
        //     $condition->where("tcs_external_employees.authorizing_number", "=", $this->numberEmployee)
        //         ->orWhere("tcs_external_employees.responsible_number", "=", $this->numberEmployee);
        // })
        ->get()
        ->toArray();

        // return $tercero;

        $response = array();

        foreach($tercero as $key => $value) {
            $auto_resp = requestFus::select(
                'name',
                'number',
                'tcs_autorizador_responsable.type AS tipo'
            )
            ->join(
                "tcs_autorizador_responsable",
                "tcs_autorizador_responsable.tcs_request_fus_id", 
                "=",
                "tcs_request_fus.id"
            )
            ->where("tcs_external_employees_id", "=", $value["id"])
            ->where("tcs_request_fus.type", "=", 1)
            ->where("tcs_autorizador_responsable.status", "=", 1)
            ->distinct()
            ->get()
            ->toArray();
            
            foreach($auto_resp as $ind => $val) {
                switch ($val["tipo"]) {
                    case 1:
                        if(!isset($value["autorizador"])) {
                            $value["autorizador"] = $val["name"]." | ".$val["number"].",";
                        } else {
                            $value["autorizador"] .= $val["name"]." | ".$val["number"].",";
                        }
                    
                        break;    
                    case 2:
                        if(!isset($value["responsable"])) {
                            $value["responsable"] = $val["name"]." | ".$val["number"].",";
                        } else {
                            $value["responsable"] .= $val["name"]." | ".$val["number"].",";
                        }
                        
                        break;
                }
            }
            
            $value["autorizador"] = substr($value["autorizador"], 0, -1);
            $value["responsable"] = substr($value["responsable"], 0, -1);
            
            $response[] = $value;
        }

        return $response;
    }

    public function getResponsablesNotificacionBaja($dias) {
        $tercero = Terceros::select(
            'tcs_autorizador_responsable.name',
            'tcs_autorizador_responsable.number',
            'tcs_autorizador_responsable.type'
        )
        ->join('tcs_cat_suppliers','tcs_external_employees.tcs_externo_proveedor','=','tcs_cat_suppliers.id')
        ->join('tcs_request_fus','tcs_request_fus.tcs_external_employees_id', '=', 'tcs_external_employees.id')
        ->join('tcs_autorizador_responsable','tcs_autorizador_responsable.tcs_request_fus_id', '=', 'tcs_request_fus.id')
        ->where('tcs_external_employees.status','=','1')
        ->where('tcs_external_employees.low_date','<=',DB::raw("(SELECT CURDATE() + INTERVAL $dias DAY)"))
        ->distinct()
        ->get()
        ->toArray();
        
        $responsables = array();

        foreach($tercero as $index => $row) {
            $responsables[] = $row["number"];
        }

        $correo = new ActivedirectoryEmployees;

        $response = array();
        $response["numbers"] = $responsables;
        $response["mails"] = $correo->getmails($responsables);

        return $response;
    }
}