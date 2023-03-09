<?php

namespace App\Http\Controllers;

use App\Models\Survey;
use App\Http\Helpers\Helper;
use Illuminate\Http\Request;
use App\Models\SurveyDefinition;
use Illuminate\Support\Collection;
use App\Models\SurveyDefinitionValue;
use App\Http\Resources\SurveyDefinitionResource;

/*
Survey Has 4 main types of inputs
    Matrix (Columns (choices), Rows (questions))'
        Rows and columns both have Value, Text (in frontend)
        we store type in Survey Definition so we can differentiate them
    Checkbox (Choices)
    Radio (Choices)
        Choices only have Text in frontend
    Boolean
    Text
*/

class SurveyController extends Controller
{
    public function create(Request $request)
    {
        $data = $request->all();
        $survey = Survey::create(['attr_id' => $data['attrID'], 'title' => $data['title']]);
        $surveyDefinitions = $this->createSurveyDefinitions($data['definitions'], $survey->id);

        foreach ($surveyDefinitions as $surveyDefinition) {
            $definition = $surveyDefinition['definition'];
            $questions = $surveyDefinition['questions'];
            $choices = $surveyDefinition['choices'];
            $typeIDS  = config('constants.surveyDefinitionValueTypeIDS');

            if ($questions->isNotEmpty() && $definition->isMatrix()) {
                $this->createSurveyDefinitionValues($questions, $typeIDS['question'], $definition->id);
            }

            if ($choices->isNotEmpty()) {
                $this->createSurveyDefinitionValues($choices, $typeIDS['choice'], $definition->id);
            }
        }
    }

    public function getSurvey()
    {
        $survey = Survey::where('attr_id', request()->route('attr_id'))->first();

        if (is_null($survey)) {
            return response()->json([
                'code' => 0,
                'message', 'კითხვარი ვერ მოიძებნა'
            ], 400);
        }

        return response()->json([
            'code' => 1,
            'message' => 'success',
            'surveyID' => $survey->id,
            'elements' => SurveyDefinitionResource::collection($survey->definitions)
        ]);
    }

    public function store(Request $request)
    {
        // TODO send survey ID and record ID from front;
        $data = $request->all();
        $definitionID = array_key_first($data);
        $questionTypeID = config('constants.surveyDefinitionValueTypeIDS.question');
        $questions = SurveyDefinitionValue::select(['id', 'survey_definition_id', 'key'])->where('survey_definition_id', $definitionID)->where('type', $questionTypeID)->get();
        $values = [...$data[$definitionID]];

        foreach ($questions as $value) {
            if (!array_key_exists($value->key, $values)) {
                $values[$value->key] = null;
            }
        }
        return  $values;
    }

    private function createSurveyDefinitions($definitions, $surveyID): Collection
    {
        $surveyDefinitions = collect();

        foreach ($definitions as $definition) {
            $definition['survey_id'] = $surveyID;

            $surveyDefinitions->push([
                'definition' => SurveyDefinition::create($definition),
                'questions' => collect($definition['questions']),
                'choices' => collect($definition['choices'])
            ]);
        }

        return $surveyDefinitions;
    }

    private function createSurveyDefinitionValues($data, $type, $surveyDefinitionID)
    {
        foreach ($data as $value) {
            $value['type'] = $type;
            $value['key'] = Helper::transformString($value['text']);
            $value['survey_definition_id'] = $surveyDefinitionID;
            SurveyDefinitionValue::create($value);
        }
    }
}
