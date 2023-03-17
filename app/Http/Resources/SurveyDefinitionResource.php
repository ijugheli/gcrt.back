<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SurveyDefinitionResource extends JsonResource
{
    public function toArray($request)
    {
        $choices = SurveyDefinitionValueResource::collection($this->choices);

        if ($this->isMatrix()) {
            $array['columns'] = $choices;
            $array['rows'] = SurveyDefinitionValueResource::collection($this->questions);
        } else {
            $array['choices'] = $choices;
        }

        return [
            'showCompletedPage' => false,
            'type' => $this->definitionType,
            'name' => strval($this->id) ,
            'title' => $this->title,
            'description' => $this->description,
            'alternateRows' => true,
            ...$array
        ];
    }
}
