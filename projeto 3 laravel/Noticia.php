<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Noticia extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'tipo',
        'titulo',
        'subtitulo',
        'corpo',
        'link',
        'imagem_capa',
        'data_noticia'
    ];

    protected $casts = [
        'data_noticia' => 'date:Y-m-d',
    ];

    /**
     * Get the comments for the blog post.
     */
    public function imagens()
    {
        return $this->hasMany(NoticiaImagem::class);
    }
}
