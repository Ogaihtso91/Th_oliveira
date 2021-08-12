<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class Blog extends Model
{
    use Notifiable, SoftDeletes;

    /*********** PARAMETERS ***********/
    protected $table = 'blog';

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'author',
    	'title',
    	'summary',
    	'image',
    	'content',
    	'theme_id',
    	'institution_id',
        'promote',
        'user_admin_id',
        'seo_url'
    ];

    protected $appends = array('blog_date_string', 'recommended');

    /*********** RELATIONS ***********/
    public function view()
    {
        return $this->hasMany(BlogView::class);
    }

    public function theme() {
        return $this->belongsTo(Theme::class, 'theme_id');
    }

    public function user_admin() {
        return $this->belongsTo(UserAdmin::class, 'user_admin_id');
    }

    public function institution() {
        return $this->belongsTo(Institution::class, 'institution_id');
    }

    public function recommends()
    {
        return $this->hasMany(BlogRecommend::class);
    }

    public function getBlogDateStringAttribute()
    {
        return \Date::parse($this->created_at)->format('d \d\e F \d\e Y');
    }

    public function getRecommendedAttribute()
    {
        return BlogRecommend::where('ip', preg_replace("/\D/", "", \App\Helpers::get_user_ip()))->where('blog_id', $this->id)->count();
    }

    public function getSeenAttribute()
    {
        return BlogView::where('ip', preg_replace("/\D/", "", \App\Helpers::get_user_ip()))->where('blog_id', $this->id)->count();
    }

    public function comments()
    {
        return $this->hasMany(BlogComment::class);
    }

}
