<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = ['titulo','data_inicial','data_final', 'reuniao_id','hora_inicio','local'];

    protected $casts = [
      'data_inicial' => 'date',
      'data_final' => 'date',
    ];

    public function reuniao() {

      return $this->hasOne(Reuniao::class, 'id');
    
    }

    public function role(){
      return $this->belongsToMany('Spatie\Permission\Models\Role');
    }

}
