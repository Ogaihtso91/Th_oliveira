<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Testimony extends Model
{
    use SoftDeletes;

    /*********** PARAMETERS ***********/
    protected $dates = ['deleted_at'];

    protected $fillable = [
    	'title',
    	'description',
    	'image',
    	'active'
    ];
}
