<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

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

    public function altaFus($tipo, $data) {
        $fus = new requestFus;
        $fus->id_generate_fus = strtotime(date("Y-m-d H:i:s"));
        $fus->description = "Se aplico la baja";
        $fus->type = $tipo;
        $fus->tcs_type_low_id = $data["id"];
        $fus->real_low_date = $data["real_low_date"];
        $fus->tcs_number_responsable_authorizer = Auth::user()->noEmployee;

        if ($fus->save()) {
            return $fus->id;
        }

        return false;
    }
}
