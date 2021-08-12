<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Documento extends Model
{
    protected $fillable = [
    	'documentos',
    	'topico_id',
    	'nomedocumento',
    	'ordenacao'
    	
	];


	public function topico() {

		return $this->belongsTo(Topico::class, 'topico_id');
	}
}
