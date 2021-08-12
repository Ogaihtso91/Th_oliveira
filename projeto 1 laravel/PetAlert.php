<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PetAlert extends Model
{
    protected $fillable = [
        'user_id', 'alert_type', 'status', 'specie', 'breed', 'color', 'photo', 'gender', 'comment', 'localization', 'last_position_lat', 'last_position_lng'
    ];

    protected $appends = [
        'slug'
    ];

    const OPEN = 'O';
    const CLOSED = 'C';

    const DISAPPEARED = 'D';
    const ABANDONED = 'A';

    const PATH_PHOTO = "assets/uploads/alerts/:filename:";

    public function comments()
    {
        return $this->hasMany(PetAlertComment::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getSlugAttribute()
    {
        $slug[] = 'Alerta';
        $slug[] = $this->attributes['specie'];
        $slug[] = $this->attributes['breed'];
        $slug[] = $this->attributes['color'];
        $slug[] = $this->attributes['alert_type'] == self::DISAPPEARED ? 'Desaparecido' : 'Abandonado';
        $slug[] = $this->attributes['id'];

        return str_slug(join(' ', $slug));
    }

    public function getGenderDescAttribute()
    {
        return Pet::GENDERS[$this->gender];
    }

    public function getAlertTypeDescAttribute()
    {
        return $this->attributes['alert_type'] == self::DISAPPEARED ? 'Desaparecido' : 'Abandonado';
    }

    public function getIsOpenAttribute()
    {
        return $this->attributes['status'] == self::OPEN;
    }

    public function makeImagePath($extension)
    {
        $dir = str_replace('/:filename:','', self::PATH_PHOTO);
        if(!is_dir($dir)) mkdir($dir, 0755, true);
        return str_replace(':filename:', md5($this->id) . '.' . $extension, self::PATH_PHOTO);
    }

    public function getUrlPhotoAttribute()
    {
        if(empty($this->photo))
            return str_replace('alerts/:filename:','default.png', self::PATH_PHOTO);
        return str_replace(':filename:', $this->photo, self::PATH_PHOTO);
    }

    public function getCreatedAtFormatAttribute()
    {
        $x = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', is_null($this->attributes['created_at']) ? '1969-01-01 01:00:00' : $this->attributes['created_at'] )->format('d/m/Y');
        return $x;
    }

}
