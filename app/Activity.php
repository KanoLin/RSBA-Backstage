<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class Activity extends Model
{
    protected $table='activity';
    
    protected $guarded=[];

    public function User()
    {
        return $this->belongsToMany('App\User')
                    ->using('App\SignUp')
                    ->withPivot('id','created_at','updated_at')
                    ->withTimestamps();
    }
}