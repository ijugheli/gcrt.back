<?php

namespace App\Http\Helpers;

use App\Models\UserAction;

class SurveyHelper
{
    // calculates SCL90 GST (General Symptomatical Index)
    // Sum of answer values divided by question count
    public static function calculateSCL90GST(\Illuminate\Support\Collection $surveyAnswers)
    {
        return round($surveyAnswers->sum('value') / 90, 4);
    }


    // Group Result is calculate by  the sum of answer values in the group divided by the group question count
    public static function calculateSCL90GroupResult($groupedAnswers, $SCL90GroupQuestionCount, $groupID)
    {
        return round($groupedAnswers[$groupID]->sum('value') / $SCL90GroupQuestionCount[$groupID], 4);
    }

    public static function getRangeTitle($ranges, $result)
    {
        foreach ($ranges as $range) {
            if ($result >= $range['from'] && $result <= $range['to']) {
                return $range['title'];
            }
        }
    }
}
