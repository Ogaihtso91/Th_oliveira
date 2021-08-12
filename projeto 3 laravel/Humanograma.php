<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Humanograma extends Model
{
    protected $table = 'humanogramas';

    protected $fillable = [
        'usuario_id',
        'rg',
        'cpf',
        'data_nascimento',
        'cep',
        'rua',
        'numero',
        'complemento',
        'bairro',
        'cidade',
        'estado',
        'cep_comercial',
        'rua_comercial',
        'numero_comercial',
        'complemento_comercial',
        'bairro_comercial',
        'cidade_comercial',
        'estado_comercial',
        'nome_secretaria',
        'telefone_secretaria',
        'email_secretaria',
        'curriculo',
        'termo_posse'
    ];

    protected $casts = [
        'data_nascimento' => 'date:d-m-Y',
    ];

    public function usuario()
    {
        return $this->belongsTo(\App\User::class);
    }

    // public function mandato()
    // {
    //     return $this->hasOne(Mandato::class);
    // }
}
