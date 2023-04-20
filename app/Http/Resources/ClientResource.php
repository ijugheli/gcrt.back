<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ClientResource extends JsonResource
{
    public function toArray($request)
    {
        $keys = ['updated_at', 'created_at', 'additional_info', 'contact', 'address'];
        $main = parent::toArray($request);

        foreach ($keys as $key => $value) {
            unset($main[$value]);
        }

        return [
            'main' => $main,
            'additional' => BaseRelationshipResource::make($this->additionalInfo),
            'contact' => BaseRelationshipResource::make($this->contact),
            'address' =>  BaseRelationshipResource::make($this->address),
        ];
    }
}
