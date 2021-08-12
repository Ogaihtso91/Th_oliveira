<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class HumanogramaUpdate extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            // Dados do usuario
            // 'usuario_id' => ['required', 'integer', 'exists:users,id'],
            'name' => ['required', 'string'],
            'foto' => ['nullable', 'file', 'mimes:png,jpg'],
            'cargo' => ['nullable', 'string'],
            'cargo_FBB' => ['required', 'array'],
            'grupo' => ['required', 'array'],
            'email' => ['required', 'email'],
            'telefone_principal' => ['required', 'string'],
            'telefone_secundario' => ['nullable', 'string'],
            'telefone_comercial' => ['nullable', 'string'],

            // Dados do humanograma
            'rg' => ['nullable'],
            'cpf' => ['nullable'],
            'data_nascimento' => ['nullable', 'date', 'before:today'],
            'cep' => ['nullable', 'string'],
            'rua' => ['nullable', 'string'],
            'numero' => ['nullable', 'string'],
            'complemento' => ['nullable', 'string'],
            'bairro' => ['nullable', 'string'],
            'cidade' => ['nullable', 'string'],
            'estado' => ['nullable', 'string'],
            'cep_comercial' => ['nullable', 'string'],
            'rua_comercial' => ['nullable', 'string'],
            'numero_comercial' => ['nullable', 'string'],
            'complemento_comercial' => ['nullable', 'string'],
            'bairro_comercial' => ['nullable', 'string'],
            'cidade_comercial' => ['nullable', 'string'],
            'estado_comercial' => ['nullable', 'string'],
            'nome_secretaria' => ['nullable', 'string'],
            'telefone_secretaria' => ['nullable', 'string'],
            'email_secretaria' => ['nullable', 'email'],
            'curriculo' => 'nullable|file|mimes:pdf|max:'. (1024*10), //10MB
            'termo_posse' => 'nullable|file|mimes:pdf|max:'. (1024*10),// 10MB
        ];
    }
}
