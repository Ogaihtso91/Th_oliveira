<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DocumentoGovernancaStoreRequest extends FormRequest
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
            'tipo_documento' => 'required|integer',
            'nome.*' => 'required|string',
            'anexo.*' => 'required|file|mimes:pdf|max:' . (1024*10),// 10MB
        ];
    }

    public function prepareForValidation()
    {
        // dd($this->all());
    }

    public function messages()
    {
        return [
            'anexo.*.max' => 'Este arquivo ultrapassou o limite de 10MB',
        ];
    }
}
