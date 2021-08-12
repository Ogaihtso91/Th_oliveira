<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SocialTecnologyKeyword extends Model
{
    /*********** PARAMETERS ***********/
    protected $table = 'social_tecnologies_keywords';

    protected $fillable = [
        'keyword_id',
        'socialtecnology_id'
    ];
}
