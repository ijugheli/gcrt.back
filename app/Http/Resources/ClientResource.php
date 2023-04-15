<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ClientResource extends JsonResource
{
    public function toArray($request)
    {
        $main = parent::toArray($request);
        unset($main['additional_info']);
        unset($main['contact']);
        unset($main['address']);
        return [
            'main' => $main,
            'additional' => $this->additionalInfo,
            'contact' => $this->contact,
            'address' => $this->address,
            // 'showCompletedPage' => false,
            // 'surveyID' => $this->id,
            // 'sectionIDS' => $sections->pluck('id'),
            // 'title' => $this->title,
            // 'description' => $this->description,
            // 'elements' => SurveySectionResource::collection($this->sections),
            // "completeText" => "შენახვა",
        ];
    }
}
