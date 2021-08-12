<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Theme extends Model
{
	protected $fillable = [
        'name',
        'link',
        'seo_url',
        'cod_lumis',
        'show',
    ];

	/*********** RELATIONS ***********/
    public function socialtecnologies()
    {
        return $this->belongsToMany(SocialTecnology::class, (new SocialTecnologyTheme)->getTable(), 'theme_id', 'socialtecnology_id');
    }

    public function events()
    {
        return $this->hasMany(Event::class);
    }

    public function users()
    {
        return $this->belongsToMany(USer::class, (new UserTheme)->getTable(), 'theme_id', 'user_id');
    }

    public function keywords()
    {
        return $this->hasMany(Keyword::class);
    }
}