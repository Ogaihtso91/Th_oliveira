<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SocialTecnologyRecommend extends Model
{
    /*********** PARAMETERS ***********/
	protected $table = 'social_tecnologies_recommends';

    protected $fillable = [
    	'user_id',
    	'socialtecnology_id'
    ];
}