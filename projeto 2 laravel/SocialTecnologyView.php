<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SocialTecnologyView extends Model
{
    /*********** PARAMETERS ***********/
    protected $table = 'social_tecnologies_view';

    protected $fillable = [
    	'ip',
    	'socialtecnology_id'
    ];
}