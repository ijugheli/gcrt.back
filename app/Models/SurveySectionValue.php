<?php

namespace App\Models;

use App\Http\Helpers\Helper;
use Illuminate\Database\Eloquent\Model;

class SurveySectionValue extends Model
{
    protected $fillable = ['id', 'survey_section_id', 'value', 'text', 'type', 'key', 'question_id', 'group_id'];

    protected $casts = ['type' => 'boolean'];

    public function surveyDefinition()
    {
        return $this->belongsTo(SurveySection::class);
    }

    public function surveyScaleGroup()
    {
        return $this->belongsTo(SurveyScaleGroup::class);
    }

    public static function saveMany($data, int $type, int $surveySectionID)
    {
        foreach ($data as $value) {
            $value['type'] = $type;
            $value['key'] = Helper::transformString($value['text']);
            if (array_key_exists('id', $value)) {
                $value['question_id'] = $value['id'];
                unset($value['id']);
            }
            $value['survey_section_id'] = $surveySectionID;
            SurveySectionValue::create($value);
        }
    }
}
