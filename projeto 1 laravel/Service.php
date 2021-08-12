<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    public function branches()
    {
        return $this->belongsToMany(Branch::class);
    }
}
