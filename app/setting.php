<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;

class setting extends model
{

    protected $table="tcs_settings";

    protected $fillable = [
        'id', 
        'settings', 
        'description',
        'status',
        'name',
        'name_large',
        'type_input_html',
        'created_at',
        'update_at'
    ];

    protected $hidden = [];

    public $timestamps = false;

    public static function settings()
    {
        $consultas = setting::get()->toArray();
        return $consultas;
    }
    public function recuperar_dias()
    {
        $sql= setting::where('name','=','day_notify_low')->get()->toArray();
        return $sql;
    }
}

?>