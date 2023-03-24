<?php

namespace App\Models;

use Illuminate\Support\Str;
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

    // public static function mapITQ() {
    //     foreach ($sections as $section) {
    //         # code...
    //     }
    // }

    public static function getITQTitle($key, $getGroupTitle = true)
    {
        return config('constants.ITQ')[$key][$getGroupTitle ? 'group_title' : 'sum_title'];
    }

    public static function getResultLevel($result, $surveyID)
    {
        $result = round($result, 1);
        $constants = config('constants');
        // to get appropriate survey range by name
        $surveyName = $constants['surveys'][$surveyID] . 'Ranges';
        return SurveyHelper::getRangeTitle($constants[$surveyName], $result);
    }

    public static function ITQResultModel(\Illuminate\Support\Collection $values, string $key, bool $getSumTitle = true)
    {
        return [
            'values' => $values,
            'group_id' => $key,
            'sum' => $values->count() > 2  ? null : $values->sum(),
            'sum_group_title' => $getSumTitle ? self::getITQTitle($key, false) : null,
            'result' => $values->contains(config('constants.meetsITQCriterias')),
            'group_title' => self::getITQTitle($key)
        ];
    }

    public static function meetsITQCriterias($array, $ITQKey)
    {
        return self::filterByITQKey($array, $ITQKey)->every(config('constants.meetsAllITQCriterias'));
    }

    public static function filterByITQKey($array, $ITQKey)
    {
        return $array->filter(function ($item, $key) use ($ITQKey) {
            return Str::startsWith($key, $ITQKey);
        });
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
