<?php

namespace App\Models\jwt;

use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class jwtDriverModel extends Authenticatable implements JWTSubject
{

    use  Notifiable;

    protected $table = 'drivers';
    // Rest omitted for brevity

    protected $primaryKey = "driver_id";

    // protected $maps =[
    //     // 'password' => 'driver_password',
    //     // 'name' => 'customer_name',
    //     'driver_password'=>'password',
    // ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function getAuthPassword(){
        return bcrypt($this->driver_password);
    }
}
