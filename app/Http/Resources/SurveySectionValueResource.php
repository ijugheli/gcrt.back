<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SurveySectionValueResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'value' => is_numeric($this->value) ? intval($this->value) : intval($this->question_id),
            'text' => $this->text,
        ];
    }
}
