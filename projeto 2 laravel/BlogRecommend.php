<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BlogRecommend extends Model
{
	/*********** PARAMETERS ***********/
    protected $table = 'blog_recommends';

    protected $fillable = [
    	'ip',
    	'blog_id'
    ];
}
