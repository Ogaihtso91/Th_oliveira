<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BlogView extends Model
{
	/*********** PARAMETERS ***********/
    protected $fillable = [
    	'ip',
    	'blog_id'
    ];

    /*********** RELATIONS ***********/
    public function blog()
    {
        return $this->belongsTo(Blog::class);
    }

}