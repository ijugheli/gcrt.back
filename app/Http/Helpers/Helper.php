<?php

namespace App\Http\Helpers;

use Carbon\Carbon;
use App\Models\UserAction;

class Helper
{
    static public function transformString($string)
    {
        $letters = ['_', '-', '(', ')', '/', '\\', ':', '.', '“', '„', '?'];

        $string = str_replace(',', '', $string);
        $string = str_replace(' ', '_', trim($string));
        $string = mb_str_split($string);

        foreach ($string as $key => $value) {
            if (in_array($value, $letters)) continue;

            $string[$key] = config('constants.alphabet')[$value];
        }

        return implode($string);
    }

    static public function saveUserAction(int $actionTypeID, int $attrID = null, int $propertyID = null, int $recordID = null, int $userID = null): void
    {
        UserAction::create([
            'user_id' => $userID ?? auth()->user()->id,
            'action_type_id' => $actionTypeID,
            'attr_id' => $attrID,
            'property_id' => $propertyID,
            'record_id' => $recordID,
        ]);
    }

    static public function formatDate($value)
    {
        $date = Carbon::createFromFormat('d/m/Y', $value, 'UTC');
        $datetime = $date->toDateTimeString();
        return is_null($value) ? null : $datetime;
    }
}
