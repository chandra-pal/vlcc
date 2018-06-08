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
//Route::get('/', ['uses' => 'DashboardController@index', 'permission' => 'index']);
//Route::auth();
Route::group([], function () {
    Route::get('/', 'DashboardController@index');
    //Route::get('/home', 'DashboardController@index');
    //Route::get('/home', 'HomeController@index');
    // Enable and install nodemodule for the AngularController
    //Route::get('/', 'AngularController@serveApp');
    //Route::get('/unsupported-browser', 'AngularController@unsupported');

    Route::get('/terms-conditions-home', 'DashboardController@termsConditions');
    Route::get('/terms-conditions-india', 'DashboardController@termsConditionsIndia');
    Route::get('/terms-conditions-bahrain', 'DashboardController@termsConditionsBahrain');
    Route::get('/terms-conditions-kuwait', 'DashboardController@termsConditionsKuwait');
    Route::get('/terms-conditions-oman', 'DashboardController@termsConditionsOman');
    Route::get('/terms-conditions-qatar', 'DashboardController@termsConditionsQatar');
    Route::get('/terms-conditions-uae', 'DashboardController@termsConditionsUAE');
});
