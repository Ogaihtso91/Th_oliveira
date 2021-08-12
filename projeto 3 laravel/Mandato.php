<?php

namespace App;

use App\Enums\Colegiados;
use Illuminate\Database\Eloquent\Model;
use App\Enums\CargosFbb;

class Mandato extends Model
{
    protected $table = 'mandatos';

    protected $casts = [
        'data_eleicao' => 'date:d-m-Y',
        'data_inicio' => 'date:d-m-Y',
        'data_termino' => 'date:d-m-Y',
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, "user_id");
    }

    public function getColegiadoFromEnums()
    {
        return !is_null($this->colegiado) ? Colegiados::getDescription((int) $this->colegiado) : null;
    }

    protected $fillable = [
        "id",
        "colegiado",
        "ato_designacao",
        "orgao_representa_comite",
        "data_eleicao",
        "data_inicio",
        "data_termino",
        "user_id",
        "titular_comite_id",
        "titular_comite_flag",
        "declaracao_bens",
        "termo_posse",
    ];

    public static function getValidate()
    {
        $messages["messages"] = [
            'colegiado.required' => 'Campo "Colegiado" em branco.',
            'data_eleicao.required' => 'Campo "Data de eleição" em branco.',
            'data_inicio.required' => 'Campo "Data de início" em branco.',
            'data_termino.required' => 'Campo "Data de término" em branco.',
            'user_id.required' => 'Campo "Data Nome" em branco.',
        ];
        $messages["validate"] = [
            'colegiado' => 'required|max:255',
            'data_eleicao' => 'required',
            'data_inicio' => 'required',
            'data_termino' => 'required',
            'user_id' => 'required',
        ];
        return $messages;
    }

    public static function salvar($dados)
    {
        if (isset($dados["id"])) {
            Mandato::find($dados["id"])->update($dados);
        } else {
            Mandato::create($dados);
        }
    }

    public function getCargos()
    {
        return !is_null($this->usuario->cargo_FBB) ? CargosFbb::getDescriptionFromModel($this->usuario->cargo_FBB) : null;
    }

    public function getCargosIds()
    {
        return !is_null($this->usuario->cargo_FBB) ? CargosFbb::getByIds($this->usuario->cargo_FBB) : null;
    }
}
