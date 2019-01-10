<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Adldap\AdldapInterface;

use Adldap\Laravel\Facades\Adldap;

class LDAPController extends Controller
{
    public function index() {
        // $con = Adldap::getProvider('tsm');
        // echo '<pre>';print_r($con);echo '</pre>';
        // $results = $con->search()->users()->get();
        // echo '<pre>';print_r($results);echo '</pre>';
        echo 'TSM<br>';
        $results = Adldap::getProvider('tsm')->search()->users()->get();
        echo '<pre>';var_dump($results);echo '</pre>';
        echo 'FILIAL<br>';
        $results = Adldap::getProvider('filial')->search()->users()->get();
        echo '<pre>';var_dump($results);echo '</pre>';
        echo 'CORP<br>';
        $results = Adldap::search()->users()->get();
        echo '<pre>';var_dump($results);echo '</pre>';
        die();

        $username = 'cpavonmu';
        $password = 'Pacemu1219&/';

        try {
            if (Adldap::auth()->attempt($username, $password)) {
                // Passed.
                echo 'Yes';
            } else {
                // Failed.
                echo 'No';
            }
        } catch (Adldap\Auth\UsernameRequiredException $e) {
            // The user didn't supply a username.
            echo '<pre>';print_r($e);echo '</pre>';
        } catch (Adldap\Auth\PasswordRequiredException $e) {
            // The user didn't supply a password.
            echo '<pre>';print_r($e);echo '</pre>';
        }

        // return view('welcome', [
        //     'users' => $ldap->search()->users()->get()
        // ]);
    }
}