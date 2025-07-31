<?php

namespace App\Http\Requests\Exam;

use App\Enums\Exam\GroupEnum;
use App\Enums\Exam\LateralityEnum;
use App\Models\Exam\Exam;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Validation\Validator;


class ExamRequest extends FormRequest
{
    /**
     * Determina se o usuário está autorizado a fazer esta requisição.
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
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255'
            ],
            'laterality' => ['nullable', new Enum(LateralityEnum::class)],
            'comment' => ['required', 'string'],
            'group' => ['required', new Enum(GroupEnum::class)],
        ];
    }

    /**
     * Regras adicionais de validação personalizadas.
     *
     * @param Validator $validator
     * @return void
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $name       = $this->input('name');
            $group      = $this->input('group');
            $laterality = $this->input('laterality');
            $examId     = $this->route('exam');

            $exists = Exam::where('name', $name)
                ->where('group', $group)
                ->where(function ($query) use ($laterality) {
                    if (is_null($laterality)) {
                        $query->whereNull('laterality');
                    } else {
                        $query->where('laterality', $laterality);
                    }
                })
                ->when($examId, fn($q) => $q->where('id', '<>', $examId))
                ->exists();

            if ($exists) {
                $validator->errors()->add(
                    'name',
                    'Já existe um exame com esse nome, grupo e lateralidade.'
                );
            }
        });
    }

    /**
     * Mensagens personalizadas para os erros de validação.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'name.required'     => 'O campo nome é obrigatório.',
            'name.string'       => 'O campo nome deve ser uma string.',
            'name.max'          => 'O campo nome deve ter no máximo 255 caracteres.',
            'name.unique'       => 'O nome do exame já está em uso.',

            'laterality.enum'   => 'O valor da lateralidade é inválido.',

            'comment.required'  => 'O campo comentário é obrigatório.',
            'comment.string'    => 'O campo comentário deve ser uma string.',

            'group.required'    => 'O campo grupo é obrigatório.',
            'group.enum'        => 'O valor do grupo é inválido.',
        ];
    }
}
