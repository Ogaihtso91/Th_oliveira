<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Breed extends Model
{
    //
    protected $fillable = [
        'name', 'species_id'
    ];

    public function species()
    {
        return $this->belongsTo(Species::class);
    }
}
