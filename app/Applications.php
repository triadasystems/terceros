<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Applications extends Model
{
    protected $table="applications";

    protected $fillable = [
        'id',
        'name',
        'alias',
        'active'
    ];

    protected $hidden = ["responsability_id", "instance_id"];

    public $timestamps = false;

    public function applicationById() {
        
    }
}
