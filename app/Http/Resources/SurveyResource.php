<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SurveyResource extends JsonResource
{
    public function toArray($request)
    {
        $sections = $this->sections;
        return [
            'showCompletedPage' => false,
            'surveyID' => $this->id,
            'sectionIDS' => $sections->pluck('id'),
            'title' => $this->title,
            'description' => $this->description,
            'elements' => SurveySectionResource::collection($this->sections),
            "completeText" => "შენახვა",
        ];
    }
}
