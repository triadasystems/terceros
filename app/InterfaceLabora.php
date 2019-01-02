<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InterfaceLabora extends Model
{
    protected $table="interface_labora";

    protected $fillable = [
        'id', 'employee_number', 'name', 'lastname1', 'lastname2', 'created', 'origen_id', 'consecutive', 'operation', 'fecha_baja', 'motivo_baja'
    ];

    public $timestamps = false;
}
