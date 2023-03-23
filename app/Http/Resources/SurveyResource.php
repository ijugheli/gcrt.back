<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SurveyResource extends JsonResource
{
    public function toArray($request)
    {

        return [
            'showCompletedPage' => false,
            'surveyID' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'alternateRows' => true,
            'elements' => SurveySectionResource::collection($this->sections),
            "completeText" => "შენახვა",
        ];
    }
}
