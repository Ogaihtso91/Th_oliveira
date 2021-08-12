<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SocialTecnologyUser extends Model
{
    /*********** PARAMETERS ***********/
	protected $table = 'social_tecnologies_users';

    protected $fillable = [
    	'user_id',
    	'socialtecnology_id'
    ];
}