<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BaseRelationshipResource extends JsonResource
{
    public function toArray($request)
    {
        $keys = ['updated_at', 'created_at'];

        $array = parent::toArray($request);

        foreach ($keys as $key => $value) {
            unset($array[$value]);
        }

        return $array;
    }
}
