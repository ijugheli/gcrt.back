<?php

namespace App\Http\Controllers;

use App\Models\Survey;
use App\Http\Helpers\Helper;
use Illuminate\Http\Request;
use App\Models\SurveyDefinition;
use Illuminate\Support\Collection;
use App\Models\SurveyDefinitionValue;
use App\Http\Resources\SurveyDefinitionResource;
use App\Models\SymptomSurveyGroup;

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
            $typeIDS  = config('constants.surveyDefinitionValueTypeIDS');

            if ($surveyDefinition['questions']->isNotEmpty() && $surveyDefinition['definition']->isMatrix()) {
                $this->createSurveyDefinitionValues($surveyDefinition['questions'], $typeIDS['question'], $surveyDefinition['definition']->id);
            }

            if ($surveyDefinition['choices']->isNotEmpty()) {
                $this->createSurveyDefinitionValues($surveyDefinition['choices'], $typeIDS['choice'], $surveyDefinition['definition']->id);
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
        $data = collect($request->all());
        $data = $data->first();
        $questionTypeID = config('constants.surveyDefinitionValueTypeIDS.question');
        $questions = SurveyDefinitionValue::select(['id', 'survey_definition_id', 'question_id', 'group_id'])->where('survey_definition_id', 1)->where('type', $questionTypeID)->get();
        $symptomGroupCount = $questions->groupBy('group_id')->map->count();
        $symptomGroups = SymptomSurveyGroup::all()->map(function ($group) {
            return [
                'group_id' => $group->id,
                'group_title' => $group->title,
            ];
        });


        $new = collect();
        $surveyAnswers = collect();

        // foreach ($questions as $key => $question) {
        //     $array = [];
        //     if (!array_key_exists($question->question_id, $data)) {
        //         $array['value'] = null;
        //     } else {
        //         $array['value'] = $data[$question->question_id];
        //     }
        //     $array['id'] = $question->question_id;
        //     $array['group_id'] = $question->group_id;
        //     $new->push($array);
        // }

        // Set all given question values
        foreach ($questions as $key => $question) {
            if (!array_key_exists($question->question_id, $data)) {
                continue;
            }

            $array = [];
            $array['value'] = $data[$question->question_id]; //  0,1,2,3,4
            $array['id'] = $question->question_id;
            $array['group_id'] = $question->group_id;
            $surveyAnswers->push($array);
        }

        // To get sum of each symptom groups
        $groupedAnswers = $surveyAnswers->groupBy('group_id');

        $results = $symptomGroups->map(function ($symptomGroup) use ($symptomGroupCount, $groupedAnswers) {
            $result = null;

            if ($groupedAnswers->offsetExists($symptomGroup['group_id'])) {
                $result = round($groupedAnswers[$symptomGroup['group_id']]->sum('value') / $symptomGroupCount[$symptomGroup['group_id']], 4);
            }

            return array_merge($symptomGroup, ['result' => $result, 'resultLevel' => $this->getResultLevel(round($result, 1))]);
        });

        // All question sum divided by question count (90)
        $gst = [
            'group_id' => null,
            'group_title' => 'დისტრესის სიმძიმის ზოგადი ინდექსი (GST)',
            'result' => round($surveyAnswers->sum('value') / 90, 4)
        ];

        $gst['resultLevel'] = $this->getResultLevel(round($gst['result'], 1));

        return response()->json(['code' => 1, 'message' => 'success', 'data' => $results->prepend($gst)]);
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

    public function getResults()
    {
    }

    private function createSurveyDefinitionValues($data, $type, $surveyDefinitionID)
    {
        foreach ($data as $value) {
            $value['type'] = $type;
            $value['key'] = Helper::transformString($value['text']);
            if (array_key_exists('id', $value)) {
                $value['question_id'] = $value['id'];
            }
            $value['survey_definition_id'] = $surveyDefinitionID;
            SurveyDefinitionValue::create($value);
        }
    }

    private function getResultLevel($result)
    {
        $ranges = [
            [0.1, 0.4, 'ძალიან დაბალი დონე'],
            [0.5, 1.4, 'დაბალი დონე'],
            [1.5, 2.4, 'საშუალო დონე'],
            [2.5, 3.4, 'აწეული დონე'],
            [3.5, 4.0, 'მაღალი დონე']
        ];

        foreach ($ranges as $range) {
            $from = $range[0];
            $to = $range[1];
            $title = $range[2];
            if ($result >= $from && $result <= $to) {
                return $title;
            }
        }
    }
}
