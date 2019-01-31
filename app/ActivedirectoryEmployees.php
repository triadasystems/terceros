<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ActivedirectoryEmployees extends Model
{
    protected $table="activedirectory_employees";

    protected $fillable = [
        'id',
        'username',
        'employee_number',
        'group',
        'name',
        'lastname1',
        'lastname2',
        'created',
        'extensionAttribute15',
        'email'
    ];

    protected $hidden = [];

    public $timestamps = false;

    public $numbersEmpNotifys;

    public function getmails($numbersEmployees) {
        $this->numbersEmpNotifys = $numbersEmployees;

        $emails = array();

        $emailsResp = ActivedirectoryEmployees::select('extensionAttribute15', 'email')
        ->where(function ($query) {
            $query->whereIn('extensionAttribute15', $this->numbersEmpNotifys); // 4802408,5600653,4108627,5600746
        })
        ->get()
        ->toArray();

        foreach ($emailsResp as $key => $value) {
            $emails[] = array("number" => $value["extensionAttribute15"], "email" => $value["email"]);
        }

        return $emails;
    }
}
