<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Keyword extends Model
{
    protected $fillable = [
        'name',
    	'status',
    	'cod_lumis',
    	'theme_id'
    ];
}
