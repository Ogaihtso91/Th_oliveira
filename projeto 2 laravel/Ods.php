<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ods extends Model
{
    use SoftDeletes;

    /*********** PARAMETERS ***********/
    protected $dates = ['deleted_at'];

    protected $fillable = [
    	'number',
    	'name',
    	'image',
    	'seo_url',
    ];

    /*********** RELATIONS ***********/
    public function socialtecnologies()
    {
        return $this->belongsToMany(SocialTecnology::class, (new SocialTecnologiesOds)->getTable(), 'ods_id', 'socialtecnology_id');
    }
}
