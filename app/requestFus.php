<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
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

        if($forma == "manual") {
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
        $fus->description = "Se aplico la baja";
        $fus->type = $tipo;
        $fus->tcs_type_low_id = $motivo;
        $fus->tcs_external_employees_id = $data["id"];
        $fus->real_low_date = $data["real_low_date"];
        $fus->tcs_number_responsable_authorizer = $noEmployee;

        if ($fus->save()) {
            return $fus->id;
        }

        return false;
    }
}
