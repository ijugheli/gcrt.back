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

    public static function getSCL90ResultLevel($result)
    {
        $result =  round($result, 1);
        foreach (config('constants.SCL90Ranges') as $range) {
            if ($result >= $range['from'] && $result <= $range['to']) {
                return $range['title'];
            }
        }
    }

    public static function getSCL90GST(\Illuminate\Support\Collection $surveyAnswers)
    {
        $gst = [
            'group_id' => null,
            'group_title' => 'დისტრესის სიმძიმის ზოგადი ინდექსი (GST)',
            'result' => SurveyHelper::calculateSCL90GST($surveyAnswers)
        ];

        $gst['resultLevel'] =  self::getSCL90ResultLevel($gst['result']);

        return $gst;
    }
}
