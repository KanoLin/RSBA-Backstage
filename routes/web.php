<?php

use Illuminate\Support\Facades\Route;







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
    return view('welcome');
});

/* Route::any('/test','RSBAController@test')->name('test');

Route::any('/{id}/test','RSBAController@test2');
 */

Route::middleware('web')->group(function(){
    
    Route::post('/api/init','RSBAUserValidateController@init');
    Route::post('/api/login','RSBAUserValidateController@login');
    Route::post('/api/signout','RSBAUserValidateController@signout');
    
    Route::middleware('IsManager')->group(function(){
        Route::post('/api/manager/publish/volunteer','RSBAController@volunteer');
        Route::post('/api/manager/publish/award','RSBAController@award');
        Route::post('/api/manager/query/{activity_id}/department','RSBAController@member_query');
        
        Route::post('/api/publisher/modify/volunteer/{activity_id}','RSBAPublisherController@modify_volunteer');
        Route::post('/api/publisher/modify/award/{activity_id}','RSBAPublisherController@modify_award');
        Route::post('/api/publisher/delete/{activity_id}','RSBAPublisherController@kill');
    });

    Route::middleware('IsInit')->group(function(){

        
    });
   
    
   // Route::post('/api/user/query/activity','RSBAController@test2');
    
    
    /*Route::post('/api/manager/query/{activity_id}/userinfo','RSBAController@test2');
    
    Route::post('/api/manager/download/{activity_id}','RSBAController@test2');
    
    Route::post('/api/user/register/{activity_id}','RSBAController@test2');
    
    
    
    
    
    
    Route::post('/api/publisher/roll/{activity_id}','RSBAController@test2'); */


});

