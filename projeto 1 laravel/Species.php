<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Species extends Model
{
    protected $fillable = [
        'name'
    ];


    public function breeds()
    {
        return $this->hasMany(Breed::class);
    }
}
