<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AwardImages extends Model
{
    //*********** PARAMETERS ***********/
    protected $table = 'award_images';

    protected $fillable = [
        'image',
        'award_id',
        /** arefa 4373 deito por marcio.rosa */
        'main'
    ];



    
}
