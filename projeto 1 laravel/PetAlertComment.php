<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PetAlertComment extends Model
{

    protected $fillable = ['pet_alert_id', 'user_id', 'comment'];


    public function replied()
    {
        return $this->belongsTo(PetAlertComment::class);
    }
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getCreatedAtFormatAttribute()
    {
        $x = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', is_null($this->attributes['created_at']) ? '1969-01-01 01:00:00' : $this->attributes['created_at'] )->format('d/m/Y H:i:s');
        return $x;
    }


}
