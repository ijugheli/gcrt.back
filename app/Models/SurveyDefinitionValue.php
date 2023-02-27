<?php

namespace App\Models;

use App\Http\Helpers\Helper;
use Illuminate\Database\Eloquent\Model;

class SurveyDefinitionValue extends Model
{
    protected $table = 'survey_definition_values';

    protected $fillable = ['id', 'survey_definition_id', 'value', 'text', 'type', 'key'];

    protected $casts = ['type' => 'boolean'];

    public function surveyDefinition()
    {
        return $this->belongsTo(SurveyDefinition::class);
    }
}
