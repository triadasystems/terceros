<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TipoBajas extends Model
{
    protected $table="tcs_type_low";

    protected $fillable = [
        'id',
        'code',
        'type'
    ];

    protected $hidden = [];

    public $timestamps = false;
    
    public function tiposBajas() {
        return TipoBajas::where("code", "<>", 0)->where("status", "=", "Activo")->get()->toArray();
    }
}
