<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SurveySectionResource extends JsonResource
{
    public function toArray($request)
    {
        $choices = SurveySectionValueResource::collection($this->choices);
        $hasTitle = $this->title != '';
        if ($this->isMatrix()) {
            $array['columns'] = $choices;
            $array['rows'] = SurveySectionValueResource::collection($this->questions);
        } else {
            $array['choices'] = $choices;
        }

        return [
            'type' => $this->sectionType,
            'name' => strval($this->id) ,
            'title' => $hasTitle ? $this->title : $this->description,
            'description' => $hasTitle ?  $this->description : null,
            'alternateRows' => true,
            "completeText" => "შენახვა",
            ...$array
        ];
    }
}
