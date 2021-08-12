<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    protected $fillable = [
        'name', 'type', 'lat', 'lng', 'district_id', 'cep'
    ];

    public function district()
    {
        return $this->belongsTo(District::class);
    }

    public function getAddressNameAttribute()
    {
        return "{$this->type} {$this->name}";
    }

    public function getGps($array = false)
    {
        return ['latitude' => $this->lat, 'longitude' => $this->lng ];
    }
}
