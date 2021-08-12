<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserTheme extends Model
{
    protected $table = 'users_themes';

    protected $fillable = [
    	'user_id',
    	'theme_id'
    ];

}
