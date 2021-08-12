<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUser extends FormRequest
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
            'name' => 'required|max:120',
            'email' => 'required|email|unique:users,email,'.$this->route('user'),
            'cargo_FBB' => 'required',
            'telefone_principal' => 'required',
            'password' => 'nullable|min:6|confirmed',
            'foto' => 'mimes:jpeg,bmp,png|max:1000',
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
            'name.required' => 'O preenchimento do campo "Nome" é obrigatório para concluir a operação.',
            'name.max' => 'Máximo de 100 caracteres permitido no campo "Nome".',
            'email.required' => 'O preenchimento do campo "E-mail" é obrigatório para concluir a operação.',
            'email.unique' => 'E-mail já cadastrado.',
            'cargo_FBB.required' => 'O preenchimento do campo "Cargo FBB" é obrigatório para concluir a operação.',
            'telefone_principal.required' => 'Campo "Telefone Principal" é obrigatório para concluir a operação.',
            'password.required' => 'O preenchimento do campo "Senha" é obrigatório para concluir a operação.',
            'password.min' => 'A senha tem que ter no mínimo 6 caracteres.',
            'password.confirmed' => 'As senhas que você digitou são diferentes.',
            'foto.mimes' => 'Extensão não permitida no campo "Foto".',
            'foto.max' => 'Tamanho máximo permitido da imagem é 1MB'
        ];

    }
}
