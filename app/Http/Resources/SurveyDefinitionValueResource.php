<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SurveyDefinitionValueResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'value' => is_numeric($this->value) ? intval($this->value) : $this->key,
            'text' => $this->text,
        ];
    }
}
