<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/
Route::controller('picasa', 'PicasaController');

Route::group(['prefix' => 'manage'], function () {

    Route::controller('picasa','Manage\PicasaController');

    Route::controller('/', 'Manage\DefaultController');
});

Route::controller('/', 'DefaultController');
