<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Medicine extends Model
{
    protected $fillable = ['name', 'pet_id', 'first_day', 'last_day', 'comment'];

    public function pet()
    {
        return $this->belongsTo(Pet::class);
    }

    public function getFirstDayFormatAttribute()
    {
        if(empty($this->attributes['first_day'])) return null;
        $x = \Carbon\Carbon::createFromFormat('Y-m-d', $this->attributes['first_day'])->format('d/m/Y');
        return $x;
    }

    public function getLastDayFormatAttribute()
    {
        if(empty($this->attributes['last_day'])) return null;
        $x = \Carbon\Carbon::createFromFormat('Y-m-d', $this->attributes['last_day'])->format('d/m/Y');
        return $x;
    }
}
