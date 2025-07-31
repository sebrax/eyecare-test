<?php

namespace App\Http\Requests\Exam;

use Illuminate\Foundation\Http\FormRequest;

class GeneratePdfRequest extends FormRequest
{
    /**
     * Permite que usuário admin faça essa requisição.
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Regras de validação para geração do PDF.
     */
    public function rules(): array
    {
        return [
            'exam_ids' => 'required|array',
            'exam_ids.*' => 'exists:exams,id',
        ];
    }

    /**
     * Mensagens personalizadas para erros de validação.
     */
    public function messages(): array
    {
        return [
            'exam_ids.required' => 'A lista de exames é obrigatória.',
            'exam_ids.*.exists' => 'Um ou mais exames são inválidos ou não existem.',
        ];
    }
}
