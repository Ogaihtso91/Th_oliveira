<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Pet extends Model
{
    protected $fillable = ['name', 'color', 'user_id','birth_date','breed_id', 'gender', 'photo', 'castrated'];
    
    const PATH_PHOTO = "assets/uploads/pets/:filename:";

    const GENDERS = ['N' => 'Não Informado', 'M' => 'Macho', 'F' => 'Fêmea'];

    public function breed() 
    {
        return $this->belongsTo(Breed::class);
    }

    public function getSlugAttribute()
    {
        $slug = "{$this->name} {$this->id}";
        return str_slug($slug);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function responsibles()
    {
        return $this->belongsToMany(User::class);
    }

    public function medicines()
    {
        return $this->hasMany(Medicine::class);
    }

    public function vaccines()
    {
        return $this->hasMany(Vaccine::class);
    }


    public function makeImagePath($extension)
    {
        $dir = str_replace('/:filename:','', self::PATH_PHOTO);
        if(!is_dir($dir)) mkdir($dir, 0755, true);
        return str_replace(':filename:', md5($this->id) . '.' . $extension, self::PATH_PHOTO);
    }

    public function getUrlPhotoAttribute()
    {
        if(is_null($this->photo))
            return str_replace('pets/:filename:','default.png', self::PATH_PHOTO);
        return str_replace(':filename:', $this->photo, self::PATH_PHOTO);
    }

    public function getGenderDescAttribute()
    {
        return $this->gender == 'M' ? 'Macho' : 'Fêmea';
    }

    public function getCastratedDescAttribute()
    {
        return $this->castrated == 'Y' ? 'Sim' : 'Não';
    }

    public function getYearsOldAttribute()
    {
        $years_old  = '';
        $birth_date = new Carbon( $this->attributes['birth_date'] );
        $now        = Carbon::now();
        $years      = $birth_date->diff($now)->y;
        $months     = $birth_date->diff($now)->m;
        $days       = $birth_date->diff($now)->d; 
        if($years > 0) $years_old .= $years . 'a ';
        if($months > 0) $years_old .= $months . 'm';
        if($months == 0 && $years == 0) {
            $years_old = $days . 'd';
        }

        return trim($years_old);

    }

    public function getBirthDateAttribute($birth_date)
    {
        return \Carbon\Carbon::createFromFormat('Y-m-d', is_null($birth_date) ? date('Y-m-d', strtotime('-1 weeks')) : $birth_date )->format('d/m/Y');
    }
}
