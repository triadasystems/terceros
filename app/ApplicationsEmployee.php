<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ApplicationsEmployee extends Model
{
    protected $table="tcs_applications_employee";

    protected $fillable = [
        'id',
        'tcs_external_employees_id',
        'applications_id'
    ];

    protected $hidden = [];

    public $timestamps = false;

    public function applicationEmployeeById($id) {
        $ApplicationsEmployee = ApplicationsEmployee::where("tcs_external_employees_id", "=", $id)->get()->toArray();

        return $ApplicationsEmployee;
    }
}
