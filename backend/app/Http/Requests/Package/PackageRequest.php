<?php

namespace App\Http\Requests\Package;

use Illuminate\Foundation\Http\FormRequest;


class PackageRequest extends FormRequest
{
    /**
     * Define se o usuário pode fazer essa requisição.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Regras de validação dos dados enviados na requisição.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'observations' => 'nullable|string',
            'exams' => 'required|array',
            'exams.*' => 'exists:exams,id',
        ];
    }

    /**
     * Mensagens personalizadas para os erros de validação.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'name.required' => 'O nome do pacote é obrigatório.',
            'name.max' => 'O nome do pacote não pode ter mais que 255 caracteres.',

            'observations.string' => 'A observação deve ser um texto válido.',

            'exams.required' => 'A lista de exames é obrigatória.',
            'exams.*.exists' => 'Um ou mais exames informados não existem.',
        ];
    }
}
