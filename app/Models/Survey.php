<?php

namespace App\Models;

use App\Http\Helpers\SurveyHelper;
use Illuminate\Database\Eloquent\Model;

class Survey extends Model
{
    protected $table = 'surveys';
    protected $fillable = ['id', 'attr_id', 'title', 'description'];
    protected $with = [
        'sections'
    ];

    public function attribute()
    {
        return $this->belongsTo(Attr::class);
    }

    public function sections()
    {
        return $this->hasMany(SurveySection::class);
    }

    public static function getResultLevel($result, $surveyID)
    {
        $result = round($result, 1);
        $constants = config('constants');
        // to get appropriate survey range by name
        $surveyName = $constants['surveys'][$surveyID] . 'Ranges';
        return SurveyHelper::getRangeTitle($constants[$surveyName], $result);
    }

    public static function getSCL90GST(\Illuminate\Support\Collection $surveyAnswers, $surveyID)
    {
        $gst = [
            'group_id' => null,
            'group_title' => 'დისტრესის სიმძიმის ზოგადი ინდექსი (GST)',
            'result' => SurveyHelper::calculateSCL90GST($surveyAnswers)
        ];

        $gst['resultLevel'] =  self::getResultLevel($gst['result'], $surveyID);

        return $gst;
    }
}
