<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SocialTecnologyDeploymentPlace extends Model
{
    /*********** PARAMETERS ***********/
    protected $table = 'social_tecnologies_deployment_places';

    protected $appends = array('fulltext_search_deployment_places');

    protected $fillable = [
        'address',
        'active',
        'neighborhood',
        'city',
        'state',
        'zipcode',
        'socialtecnology_id',
        'cod_lumis'
    ];

    /*********** RELATIONS ***********/
    public function socialtecnology()
    {
        return $this->belongsTo(SocialTecnology::class, 'socialtecnology_id');
    }

    /*********** GET CUSTOM PARAMETERS ***********/
    public function getFulltextSearchDeploymentPlacesAttribute()
    {
        $fulltext = "";

        if($this->address)      $fulltext .= $this->address;
        if($this->neighborhood) $fulltext .= (strlen($fulltext) > 0 ? ", " : "").$this->neighborhood;
        if($this->city)         $fulltext .= (strlen($fulltext) > 0 ? ", " : "").$this->city;
        if($this->state)        $fulltext .= (strlen($fulltext) > 0 ? ", " : "").$this->state;
        if($this->zipcode)      $fulltext .= (strlen($fulltext) > 0 ? ", " : "").$this->zipcode;

        return $fulltext;
    }
}
