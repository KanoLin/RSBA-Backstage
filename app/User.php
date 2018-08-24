<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $table='user';
    
    protected $guarded=[];

    public function Activity()
    {
        return $this->belongsToMany('App\Activity')
                    ->using('App\SignUp')
                    ->withPivot('id','created_at','updated_at')
                    ->withTimestamps();
    }
}
