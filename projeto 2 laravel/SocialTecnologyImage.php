<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SocialTecnologyImage extends Model
{
    /*********** PARAMETERS ***********/
    protected $table = 'social_tecnologies_images';

    protected $fillable = [
        'image',
        'socialtecnology_id',
        /** arefa 4373 deito por marcio.rosa */
        'main'
    ];
}