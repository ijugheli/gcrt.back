<?php

namespace App\Models;

use App\Http\Helpers\Helper;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;

class SurveySection extends Model
{
    protected $fillable = ['id', 'survey_id', 'title', 'description', 'type', 'order_id'];

    protected $with = ['questions', 'choices'];

    public function survey()
    {
        return $this->belongsTo(Survey::class);
    }

    public function questions()
    {
        $type = config('constants.surveySectionValueTypeIDS.question');
        return $this->hasMany(SurveySectionValue::class, 'survey_section_id', 'id')->where('type', $type);
    }

    public function choices()
    {
        $type = config('constants.surveySectionValueTypeIDS.choice');
        return $this->hasMany(SurveySectionValue::class, 'survey_section_id', 'id')->where('type', $type);
    }

    public function getSectionTypeAttribute()
    {
        return config('constants.surveyQuestionTypes')[$this->type];
    }

    public function isMatrix(): bool
    {
        return $this->type == config('constants.surveyQuestionTypeIDS.matrix');
    }

    public function isBoolean(): bool
    {
        return $this->type == config('constants.surveyQuestionTypeIDS.boolean');
    }

    public function isRadio(): bool
    {
        return $this->type == config('constants.surveyQuestionTypeIDS.radiogroup');
    }

    public function isCheckbox(): bool
    {
        return $this->type == config('constants.surveyQuestionTypeIDS.checkbox');
    }

    public static function saveMany($sections, $surveyID): Collection
    {
        $surveySections = collect();

        foreach ($sections as $section) {
            $section['survey_id'] = $surveyID;
            $temp = ['section' => self::create($section)];
            $keys = ['questions', 'choices'];

            foreach ($keys as $value) {
                $temp[$value] =  array_key_exists($value, $section) ? collect($section[$value]) : collect();
            }

            $surveySections->push($temp);
        }

        return $surveySections;
    }
}
