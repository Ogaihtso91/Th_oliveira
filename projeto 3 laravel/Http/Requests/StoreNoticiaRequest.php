<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreNoticiaRequest extends FormRequest
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
            'titulo' => 'required',
            'tipo'   => 'required',
            'corpo'  => 'required_if:tipo,1|required_if:tipo,2',
            'link'   => 'nullable|required_if:tipo,3|url',
            'imagem_capa' => 'mimes:jpeg,png,jpg,gif,svg|max:2048',
        ];
    }

        /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'titulo.required'   => 'O preenchimento do campo "Título" é obrigatório para concluir a operação.',
            'tipo.required'     => 'O preenchimento do campo "Tipo" é obrigatório para concluir a operação.',
            'corpo.required_if' => 'O preenchimento do campo "Corpo da Notícia" é obrigatório para concluir a operação.',
            'link.required_if'  => 'O preenchimento do campo "Link da Matéria" é obrigatório para concluir a operação.',
            'link.url'          => 'O preenchimento do campo "Link da Matéria" deve ser um endereço válido.',
            'imagem_capa.image' => 'O campo "Foto da Capa" precisar ser algum arquivo de imagem.',
            'imagem_capa.mimes' => 'No campo "Foto da Capa" as seguintes extensões são permitidas: jpeg,png,jpg,gif,svg.',

        ];

    }



}
