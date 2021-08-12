<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EventUser extends Model
{
    /*********** PARAMETERS ***********/
    protected $table = 'events_users';

    protected $fillable = [
    	'event_id',
    	'user_id'
    ];
}