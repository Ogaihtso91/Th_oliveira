<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TypeProvider extends Model
{

    public function serviceProvider()
    {
        return $this->hasMany(ServiceProvider::class);
    }

}
