<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SocialTecnologyPartner extends Model
{
    protected $table = 'social_tecnology_partner';

    protected $fillable = [
    	'institution_id',
        'socialtecnology_id',
        'acting',
        'institution_name'
    ];
}
