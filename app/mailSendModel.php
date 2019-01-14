<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class mailSendModel extends Model
{
    protected $table = 'mails';
    protected $primaryKey = 'id';
    protected $fillable=[
        "correo", "automatizacion", "bajas", "tcs_terceros_baja", "tcs_terceros_baja_auth_resp"
        ];
}