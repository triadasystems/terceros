<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AutorizadorResponsable extends Model
{
    protected $table = "tcs_autorizador_responsable";

    protected $fillable = [
        'id',
        'name',
        'number',
        'type',
        'status',
        'tcs_request_fus_id',
    ];

    protected $hidden = [];

    public $timestamps = false;
}
