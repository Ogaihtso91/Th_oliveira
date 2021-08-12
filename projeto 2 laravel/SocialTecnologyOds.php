<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SocialTecnologyOds extends Model
{
    /*********** PARAMETERS ***********/
    protected $table = 'social_tecnologies_ods';

    protected $fillable = [
        'ods_id',
        'socialtecnology_id'
    ];
}