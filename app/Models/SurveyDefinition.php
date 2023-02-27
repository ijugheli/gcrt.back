<?php

namespace App\Models;

use App\Http\Helpers\Helper;
use Illuminate\Database\Eloquent\Model;

class SurveyDefinition extends Model
{
    protected $table = 'survey_definitions';
    protected $fillable = ['id', 'survey_id', 'title', 'description', 'type', 'order_id'];


    public function survey()
    {
        return $this->belongsTo(Survey::class);
    }

    public function questions()
    {
        $type = config('constants.surveyDefinitionValueTypeIDS.question');
        return $this->hasMany(SurveyDefinitionValue::class, 'survey_definition_id', 'id')->where('type', $type);
    }

    public function choices()
    {
        $type = config('constants.surveyDefinitionValueTypeIDS.choice');
        return $this->hasMany(SurveyDefinitionValue::class, 'survey_definition_id', 'id')->where('type', $type);
    }

    public function getDefinitionTypeAttribute()
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
        return $this->type == config('constants.surveyQuestionTypeIDS..radiogroup');
    }

    public function isCheckbox(): bool
    {
        return $this->type == config('constants.surveyQuestionTypeIDS..checkbox');
    }
}
