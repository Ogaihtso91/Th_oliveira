<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserFavorite extends Model
{
    /*********** PARAMETERS ***********/
	protected $table = 'users_favorite_users';

    protected $fillable = [
    	'user_id',
    	'fav_user_id'
    ];
}