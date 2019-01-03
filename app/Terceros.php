<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

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
        return Terceros::where("authorizing_number", "=", $noBadge)->get()->toArray();
    }
}