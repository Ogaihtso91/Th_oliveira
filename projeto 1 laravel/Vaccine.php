<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Vaccine extends Model
{
    protected $fillable = ['name', 'pet_id', 'date', 'remember_date', 'comment'];

    public function pet()
    {
        return $this->belongsTo(Pet::class);
    }

    public function getDateFormatAttribute()
    {
        if(empty($this->attributes['date'])) return null;

        $x = \Carbon\Carbon::createFromFormat('Y-m-d', $this->attributes['date'])->format('d/m/Y');
        return $x;
    }

    public function getRememberDateFormatAttribute()
    {
        if(empty($this->attributes['remember_date'])) return null;

        $x = \Carbon\Carbon::createFromFormat('Y-m-d', $this->attributes['remember_date'])->format('d/m/Y');
        return $x;
    }
}
