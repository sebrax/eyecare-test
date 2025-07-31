<?php

namespace App\Http\Resources\Package;

use Illuminate\Http\Resources\Json\JsonResource;

class PackageResource extends JsonResource
{
    /**
     * Transformar o recurso em um array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'observations' => $this->observations,
            'exams' => $this->exams ? $this->exams->map(fn($exam) => [
                'id' => $exam->id,
                'name' => $exam->name,
                'laterality' => $exam->laterality?->value,
                'comment' => $exam->comment,
                'group' => $exam->group?->value,
            ]) : [],
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
        ];
    }
}
