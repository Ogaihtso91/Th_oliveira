<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Download extends Model
{
    protected $fillable = [
    	
    	'login',
    	'arquivo',
        'topico',
    	'conselho_id',
        'nome'
    	    	
	];


	public function reuniao() {

		return $this->belongsTo(Reuniao::class, 'conselho_id');
	}

}
