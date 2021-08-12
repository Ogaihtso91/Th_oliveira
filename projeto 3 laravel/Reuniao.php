<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Topico;


class Reuniao extends Model
{
    
    protected $table = 'reunioes';

    protected $fillable = [

    	'finalidade',
    	'data',
    	'grupo',
        'arquivar',
        'votacao_eletronica',
        'hora_inicio',
        'hora_fim',
    	'created_at',
    	'update_at'
    ];

    public function topico(){

        //pode ter vários registros
        return $this->hasMany(Topico::class, 'conselho_id');

    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'reunioes_user')
            ->withPivot('participacao', 'tipo_participacao');
    }

    public function download(){

        //pode ter vários registros
        return $this->hasMany(Download::class, 'conselho_id');

    }

    public function event(){

        return $this->hasOne(Event::class, 'reuniao_id');

    }

}
