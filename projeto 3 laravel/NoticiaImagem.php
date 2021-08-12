<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class NoticiaImagem extends Model
{

    protected $table = 'noticias_imagens';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'noticia_id',
        'galeria_id',
        'imagem'
    ];

}
