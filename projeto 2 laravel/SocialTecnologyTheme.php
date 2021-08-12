<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SocialTecnologyTheme extends Model
{
    /*********** PARAMETERS ***********/
    protected $table = 'social_tecnologies_themes';

    protected $fillable = [
        'theme_id',
        'socialtecnology_id'
    ];
}
