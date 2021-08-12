<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserPosition extends Model
{
    protected $fillable = ['position_address', 'lat', 'lng', 'user_id'];


    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
