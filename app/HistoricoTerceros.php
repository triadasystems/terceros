<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class HistoricoTerceros extends Model
{
    protected $table="tcs_external_employees_hist";

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
        'tcs_fus_ext_hist',
        'tcs_applications_ids'
    ];

    protected $hidden = [
        'tcs_subfijo_id', 'tcs_externo_proveedor',
    ];

    public $timestamps = false;

    public function altaHistoricoTercero($data, $idFus) {
        $historicoTerceros = new HistoricoTerceros;
        $historicoTerceros->id_external = $data["id_external"];
        $historicoTerceros->name = $data["name"];
        $historicoTerceros->lastname1 = $data["lastname1"];
        $historicoTerceros->lastname2 = $data["lastname2"];
        $historicoTerceros->initial_date = $data["initial_date"];
        $historicoTerceros->low_date = $data["low_date"];
        $historicoTerceros->badge_number = $data["badge_number"];
        $historicoTerceros->email = $data["email"];
        $historicoTerceros->authorizing_name = $data["authorizing_name"];
        $historicoTerceros->authorizing_number = $data["authorizing_number"];
        $historicoTerceros->responsible_name = $data["responsible_name"];
        $historicoTerceros->responsible_number = $data["responsible_number"];
        $historicoTerceros->created_at = $data["created_at"];
        $historicoTerceros->status = $data["status"];
        $historicoTerceros->tcs_fus_ext_hist = $idFus;
        $historicoTerceros->tcs_applications_ids = $data["tcs_applications_ids"];
        $historicoTerceros->tcs_subfijo_id = $data["tcs_subfijo_id"];
        $historicoTerceros->tcs_externo_proveedor = $data["tcs_externo_proveedor"];
        
        $historicoTerceros->save();
    }
}
