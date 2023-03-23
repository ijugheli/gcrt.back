<?php

namespace App\Models;

use App\Http\Helpers\Helper;
use Illuminate\Database\Eloquent\Model;

class SurveySectionValue extends Model
{
    protected $fillable = ['id', 'survey_section_id', 'value', 'text', 'type', 'key', 'question_id','group_id'];

    protected $casts = ['type' => 'boolean'];

    public function surveyDefinition()
    {
        return $this->belongsTo(SurveySection::class);
    }

    public function surveyScaleGroup()
    {
        return $this->belongsTo(SurveyScaleGroup::class);
    }
}
