<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    const SERVICE_PROVIDER  = 'P';
    const USER              = 'U';
    const PATH_PHOTO = "assets/uploads/users/:filename:";
   
    protected $fillable = [
        'name', 'email', 'password', 'user_type', 'service_provider_id', 'photo'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];


    public function serviceProvider()
    {
        return $this->belongsTo(ServiceProvider::class, 'service_provider_id');
    }

    public function getIsServiceProviderAttribute()
    {
        return $this->attributes['user_type'] == self::SERVICE_PROVIDER;
    }

    public function alerts()
    {
        return $this->hasMany(PetAlert::class);
    }

    public function locations()
    {
        return $this->hasMany(UserPosition::class);
    }

    public function pets()
    {
        return $this->belongsToMany(Pet::class);
    }

    public function makeImagePath($extension)
    {
        $dir = str_replace('/:filename:','', self::PATH_PHOTO);
        if(!is_dir($dir)) mkdir($dir, 0755, true);
        return str_replace(':filename:', md5($this->id) . '.' . $extension, self::PATH_PHOTO);
    }

    public function getUrlPhotoAttribute()
    {
        if(is_null($this->photo))
            return str_replace('users/:filename:','default.png', self::PATH_PHOTO);
        return str_replace(':filename:', $this->photo, self::PATH_PHOTO);
    }

}
