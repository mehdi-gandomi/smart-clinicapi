<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AssessmentSeriesResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'series_id' => $this->series_id,
            'title' => $this->title,
            'description' => $this->description,
            'order' => $this->order,
            'questions' => $this->questions->map(function ($question) {
                return [
                    'question_id' => $question->question_id,
                    'text' => $question->text,
                    'type' => $question->type,
                    'options' => $question->options,
                    'required' => $question->required,
                    'order' => $question->order,
                ];
            })
        ];
    }
}
