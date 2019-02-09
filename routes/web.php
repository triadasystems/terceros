<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    // return view('welcome');
    return redirect()->route('login');

});
Route::get('/ldap/prueba','LDAPController@index')->name('ldap_prueba');
// Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

// Pruebas
Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');
Route::post('login', 'Auth\LoginController@login');
Route::post('logout', 'Auth\LoginController@logout')->name('logout');

// Terceros
// Route::group(['prefix' => 'terceros', 'middleware' => 'userProfileInactivo'], function(){
Route::group(['prefix' => 'terceros'], function(){
    Route::get('/tercerosasignados', 'TercerosController@index')->name('subordinados');
    Route::get('/datatercerosasignados','TercerosController@tercerosAsignados')->name('tercerosAsignados.data');

    Route::post('/bajatercero', 'TercerosController@bajatercero')->name('bajatercero');
});

Route::get('bajaautomatica', 'BajasautomaticasController@bajasAutomaticas')->name('bajasdiarias');
//notificación de fecha de vencimiento
Route::group(['prefix'=>'sendmail'], function()
{
    Route::get('/vencimiento','NotificacionController@not_caducidad')->name('vencimientoTerceros');
    Route::get('/vencimientobyresponsable','NotificacionController@not_caducidad_auth_resp')->name('vencimientoTercerosPorResponsables');
    Route::get('/vencimientobyfus','NotificacionController@fus_vence')->name('vencimientoFUS');
});