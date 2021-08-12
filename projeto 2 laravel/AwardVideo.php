<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AwardVideo extends Model
{
    protected $table = 'award_videos';
    
    protected $fillable = [
        'video_url',
        'award_id'
    ];
    
    // Parâmetros Customizados
    protected $appends = array('video_info', 'video_id');

    /*********** GET CUSTOM PARAMETERS ***********/
    public function getVideoInfoAttribute()
    {
        // Busca as informações do vídeo no youtube
        return Helpers::retrieve_youtube_information($this->video_url);
    }
    public function getVideoIDAttribute()
    {
        // Retira o ID do vídeo da url
        preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $this->video_url, $match);
        return $match[1];
    }

    public function award()
    {
        return $this->belongsTo(Award::class);
    }
}
