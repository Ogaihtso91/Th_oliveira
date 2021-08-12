<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Auth;

class NotificationsConfig extends Model
{
    protected $table = 'notifications_config';

    protected $fillable = [
    	'type',
    	'notifiable_type',
    	'notifiable_id',
    	'silenced',
    ];

   public static function getUserNotificationConfig($id) {
      return self::where('notifiable_id', $id)
	        ->where('notifiable_type', get_class(User::find($id)))
	        ->get()
	        ->keyBy('type')
	        ->toArray();
   }

}
