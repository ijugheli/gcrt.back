<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CaseResource extends JsonResource
{
    public function toArray($request)
    {
        $keys = [
            'updated_at', 'created_at', 'forms_of_violences', 'care_plans', 'diagnoses',  'referrals', 'consultations'
        ];

        $case = parent::toArray($request);

        foreach ($keys as $value) {
            unset($case[$value]);
        }

        return [
            'case' => $case,
            'forms_of_violences' =>  BaseRelationshipResource::make($this->formsOfViolences),
            'care_plans' =>  BaseRelationshipResource::make($this->carePlans),
            'diagnoses' =>  BaseRelationshipResource::make($this->diagnoses),
            'referrals' =>  BaseRelationshipResource::make($this->referrals),
            'consultations' =>   BaseRelationshipResource::make($this->consultations),
        ];
    }
}
