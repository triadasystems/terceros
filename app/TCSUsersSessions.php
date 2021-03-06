<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class TCSUsersSessions extends Authenticatable
{
    protected $table = "tcs_users_sessions";

    protected $fillable = [
        'id', 'name', 'mail', 'noEmployee', 'create_at', 'update_at'
    ];

    protected $hidden = [
        'remember_token',
    ];

    public $timestamps = false;

    // Validar no. empleado
    public function validateNumberEmployee($email) {
        return TCSUsersSessions::where('email', $email)->first();
    }
}
