<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Reuniao;

class Topico extends Model {

    protected $fillable = [
        'categoria',
        'ordenacao',
        'titulo',
        'conselho_id',
        'categoria_ordenacao'
    ];

    public function reuniao() {
        //pertence a tabela conselho
        return $this->belongsTo(Reuniao::class, 'conselho_id');
    }

    public function documento() {

        return $this->hasMany(Documento::class, 'topico_id');
    }

}
