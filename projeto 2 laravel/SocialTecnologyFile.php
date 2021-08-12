<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SocialTecnologyFile extends Model
{
    /*********** PARAMETERS ***********/
    protected $table = 'social_tecnologies_files';

    protected $fillable = [
        'file',
        'file_caption',
        'socialtecnology_id'
    ];
}