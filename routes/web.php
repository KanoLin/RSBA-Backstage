<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

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


Route::any('/download/{id}','RSBAExportController@export');
Route::any('/time',function(){
    return date('Y-m-d H:i:s');
});

Route::middleware('web')->group(function(){
    
    Route::post('/api/init','RSBAUserValidateController@init');
    Route::post('/api/login','RSBAUserValidateController@login');
    Route::post('/api/signout','RSBAUserValidateController@signout');
    
    Route::middleware('manager')->group(function(){
        Route::middleware('validate:0')->group(function(){
        
        Route::post('/api/manager/publish/volunteer','RSBAController@volunteer');
        Route::post('/api/manager/publish/award','RSBAController@award');
        });
        Route::post('/api/manager/query/{activity_id}/department','RSBAController@member_query');
        
        Route::middleware('validate:1')->group(function(){
        Route::post('/api/publisher/modify/volunteer/{activity_id}','RSBAPublisherController@modify_volunteer');
        Route::post('/api/publisher/modify/award/{activity_id}','RSBAPublisherController@modify_award');
        });
        Route::post('/api/publisher/delete/{activity_id}','RSBAPublisherController@kill');
        Route::post('/api/publisher/modify/image/{activity_id}','RSBAPublisherController@upload_img');
        

        Route::post('/api/manager/query/{activity_id}/userinfo','RSBAController@userinfo_query');
        
        Route::post('/download/{activity_id}','RSBAExportController@export');
        Route::post('/daolnwod','RSBAExportController@export0');
    });
    
    Route::middleware('init')->group(function(){
        Route::post('/api/user/query/activity','RSBAUserController@activity_query');
        Route::post('/api/user/register/{activity_id}','RSBAUserController@register');
        Route::get('/activityimg/{activity_id?}',function($id='0'){
            //return \File::get(storage_path().'/app/RSBA-img/'.$id);
            return Storage::exists('/RSBA-img'.$id)?Storage::get('/RSBA-img/'.$id):Storage::get('/RSBA-img/0');
        });
    });

});

