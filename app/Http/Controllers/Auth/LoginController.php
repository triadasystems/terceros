<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Adldap\Laravel\Facades\Adldap;
// use Adldap\AdldapInterface;

use App\TCSUsersSessions;
use App\InterfaceLabora;
use App\Comparelaboraconcilia;
// use Illuminate\Support\Facades\DB;

class LoginController extends Controller    
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    // protected $redirectTo = '/home';
    protected $redirectTo = '/terceros/tercerosasignados';  // EVP

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    // public function username()
    // {
    //     return config('ldap_auth.usernames.eloquent');
    // }

    protected function validateLogin(Request $request)
    {
        $this->validate($request, [
            'username' => 'required|email',
            'extensionAttribute15' => 'required|integer',
            'password' => 'required|string',
        ]);
    }

    protected function attemptLogin(Request $request)
    {
        $response = array();
        $credentials = $request->only('username', 'password', 'extensionAttribute15', '_token');
                
        $token = $credentials['_token'];
        $email = $credentials['username'];
        $username = str_replace('@televisa.com.mx', '', $credentials['username']);
        $password = $credentials['password'];
        $noEmployee = $credentials['extensionAttribute15'];

        // $user_format = env('LDAP_USER_FORMAT', 'cn=%s,'.env('LDAP_BASE_DN', ''));
        // $userdn = sprintf($user_format, $username);

        // you might need this, as reported in
        // [#14](https://github.com/jotaelesalinas/laravel-simple-ldap-auth/issues/14):
        // Adldap::auth()->bind($userdn, $password);

        if (Adldap::getProvider('default')->auth()->attempt($username, $password, $bindAsUser = true)) {
            $auth = true;
            $datos = Adldap::getProvider('default')->search()->users()->find($username);
        } else if (Adldap::getProvider('filial')->auth()->attempt($username, $password, $bindAsUser = true)) {
            $auth = true;
            $datos = Adldap::getProvider('filial')->search()->users()->find($username);
        } else if(Adldap::getProvider('tsm')->auth()->attempt($username, $password, $bindAsUser = true)) {
            $auth = true;
            $datos = Adldap::getProvider('tsm')->search()->users()->find($username);
        } else {
            $auth = false;
        }

        if (!isset($datos["attributes"]["extensionattribute15"])) {
            // if($noEmployee == 2046132) {
            //     $response['noEmp'] = true;
            // } else {
                $response['status'] = false;
                $response['noEmp'] = false;

                return $response;
            // }
        } else {
            if(count($datos["attributes"]["extensionattribute15"]) > 0) {
                $response['noEmp'] = true;
            } else {
                $response['status'] = false;
                $response['noEmp'] = false;

                return $response;
            }
        }

        if ($auth === true) {
            // the user exists in the LDAP server, with the provided password

            $TCSUsersSessions = new TCSUsersSessions;
            // $user = $TCSUsersSessions->validateNumberEmployee($email);
            
            // if (!$user) {
                // the user doesn't exist in the local database, so we have to create one
                
                // $user = new \App\User();
            $user = $TCSUsersSessions;
            $user->email = $email;
            $user->name = $username;
            $user->noEmployee = $noEmployee;
                // $user->remember_token = $token;
                
            // you can skip this if there are no extra attributes to read from the LDAP server
            // or you can move it below this if(!$user) block if you want to keep the user always
            // in sync with the LDAP server 
            $sync_attrs = $this->retrieveSyncAttributes($username);
            
            if(!empty($sync_attrs)) {
                foreach ($sync_attrs as $field => $value) {
                    $user->$field = $value !== null ? $value : '';
                }
            }
                // $user->save();
            // }

            // echo '<pre>';print_r($user);echo '</pre>';
            // die();
            // by logging the user we create the session, so there is no need to login again (in the configured time).
            // pass false as second parameter if you want to force the session to expire when the user closes the browser.
            // have a look at the section 'session lifetime' in `config/session.php` for more options.

            $this->guard()->login($user, true);

            // $user = Adldap::search()->users()->find($username);

            // if ($user['exists'] == 1) {
            //     $sync_attrs = $this->retrieveSyncAttributes($email);
            // }

            $response['status'] = true;
        }

        // the user doesn't exist in the LDAP server or the password is wrong
        // log error
        $response['status'] = false;

        return $response;
    }

    protected function retrieveSyncAttributes($username)
    {
        $ldapuser = Adldap::search()->where('userprincipalname', '=', $username)->first();
        
        if ( !$ldapuser ) {
            // log error
            return false;
        }
        // if you want to see the list of available attributes in your specific LDAP server:
        // var_dump($ldapuser->attributes); exit;

        // needed if any attribute is not directly accessible via a method call.
        // attributes in \Adldap\Models\User are protected, so we will need
        // to retrieve them using reflection.
        $ldapuser_attrs = null;

        $attrs = [];
        
        foreach (config('ldap_auth.sync_attributes') as $local_attr => $ldap_attr) {
            if ( $local_attr == 'username' ) {
                continue;
            }

            $method = 'get' . $ldap_attr;
            if (method_exists($ldapuser, $method)) {
                $attrs[$local_attr] = $ldapuser->$method();
                continue;
            }

            if ($ldapuser_attrs === null) {
                $ldapuser_attrs = self::accessProtected($ldapuser, 'attributes');
            }

            if (!isset($ldapuser_attrs[$ldap_attr])) {
                // an exception could be thrown
                $attrs[$local_attr] = null;
                continue;
            }

            if (!is_array($ldapuser_attrs[$ldap_attr])) {
                $attrs[$local_attr] = $ldapuser_attrs[$ldap_attr];
            }

            if (count($ldapuser_attrs[$ldap_attr]) == 0) {
                // an exception could be thrown
                $attrs[$local_attr] = null;
                continue;
            }

            // now it returns the first item, but it could return
            // a comma-separated string or any other thing that suits you better
            $attrs[$local_attr] = $ldapuser_attrs[$ldap_attr][0];
            //$attrs[$local_attr] = implode(',', $ldapuser_attrs[$ldap_attr]);
        }
        
        return $attrs;
    }

    protected static function accessProtected ($obj, $prop)
    {
        $reflection = new \ReflectionClass($obj);
        $property = $reflection->getProperty($prop);
        $property->setAccessible(true);
        return $property->getValue($obj);
    }
}
