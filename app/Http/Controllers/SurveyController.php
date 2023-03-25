<?php

namespace App\Http\Controllers;

use App\Models\Survey;
use Illuminate\Http\Request;
use App\Models\SurveySection;
use App\Http\Helpers\SurveyHelper;
use App\Models\SurveySectionValue;
use App\Models\SymptomSurveyGroup;

use App\Http\Resources\SurveyResource;

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
        $surveySections = SurveySection::saveMany($data['definitions'], $survey->id);

        foreach ($surveySections as $section) {
            $typeIDS  = config('constants.surveySectionValueTypeIDS');

            if ($section['questions']->isNotEmpty() && $section['section']->isMatrix()) {
                SurveySectionValue::saveMany($section['questions'], $typeIDS['question'], $section['section']->id);
            }

            if ($section['choices']->isNotEmpty()) {
                SurveySectionValue::saveMany($section['choices'], $typeIDS['choice'], $section['section']->id);
            }
        }
    }

    public function list()
    {
        return response()->json([
            'code' => 1,
            'message' => 'success',
            'data' => SurveyResource::collection(Survey::all()),
        ]);
    }

    public function store(Request $request)
    {
        $request = $request->all();
        $surveyID = $request['surveyID'];
        $data = collect($request['data']);
        $surveyIDS = config('constants.surveyIDS');

        switch ($surveyID) {
            case $surveyIDS['SCL90']:
                return $this->SCL90Handler($data, $surveyID);
                // case $surveyIDS['ERQ']:
                // return response()->json();
            case $surveyIDS['GAD7']:
                return $this->GAD7AndPHQHandler($data,  $surveyID);
                // case $surveyIDS['LEC5']:
            case $surveyIDS['PHQ9']:
            case $surveyIDS['PHQ15']:
                return $this->GAD7AndPHQHandler($data, $surveyID);
            case $surveyIDS['ITQ']:
                return $this->ITCHandler($data->map(function ($item) {
                    return collect($item);
                }));
                // case $surveyIDS['CAPS5']:
            default:
                return response()->json();
        }
    }

    // GAD7 AND PHQ SURVEYS RESULTS ARE ALL ANSWERS SUMMED UPM THEN WE GET THE RESULTS RANGE
    private function GAD7AndPHQHandler($data, $surveyID)
    {
        $result = $data->flatten()->sum();

        return response()->json([
            'code' => 1,
            'message' => 'success',
            'data' => [
                0 => [
                    'result' => $result,
                    'resultLevel' => Survey::getResultLevel($result, $surveyID)
                ]
            ]
        ]);
    }

    /*
        p1-6 and c1-6 logic
            If (p1 >= 2 or p2 >=2) then meets X criteria
        p7-9 and c7-9 logic
            If (p7 >= 2 or p8 >= 2 or p9 >= 2) then meets X criteria
        PTSA
            If all P criterias are met && !TOD
        TOD
            If all C criterias are met
        KPTSA
            If all criterias are met
    */
    private function ITCHandler($data)
    {
        $results = collect([]);
        $keys = ['P' => $data[11], 'C' => $data[13], 'P7_9' => $data[12], 'C7_9' => $data[14]];

        foreach ($keys as $itq => $data) {
            // if P7_9 or C7_9
            if (strlen($itq) != 1) {
                $results[$itq] = Survey::ITQResultModel($data->values(), $itq, false);
                continue;
            }

            for ($i = 1; $i < count($data); $i += 2) {
                $values = collect([$data[$i], $data[$i + 1]]); // values to sum and check criteria
                $key = $itq . $i . '_' . $i + 1; // P1_2 P3_4
                $results[$key] = Survey::ITQResultModel($values, $key);
            }
        }

        $hasKPTSA = $results->every(config('constants.meetsAllITQCriterias'));
        $hasPTSA = Survey::meetsITQCriterias($results, 'P');
        $hasTOD = Survey::meetsITQCriterias($results, 'C');

        $results['KPTSA'] = ['group_title' => 'KPTSA', 'key' => 'KPTSA', 'result' => $hasKPTSA];
        $results['PTSA']  = ['group_title' => 'PTSA', 'key' => 'PTSA', 'sum' => Survey::filterByITQKey($results, 'P')->pluck('sum')->sum(), 'result' => $hasPTSA && !$hasTOD];
        $results['TOD']  = ['group_title' => 'TOD', 'key' => 'TOD', 'sum' => Survey::filterByITQKey($results, 'C')->pluck('sum')->sum(), 'result' => $hasTOD];

        return response()->json(['code' => 1, 'message' => 'success', 'data' => $results->values()]);
    }

    // SCL90 ANSWERS ARE DIVIDED BY GROUPS AND GST, groups results are : groupAnswerSum / groupQuestionCount, gst is all answerSum / questionCount
    private function SCL90Handler($data, $surveyID)
    {
        $data = $data->first();
        $questionTypeID = config('constants.surveySectionValueTypeIDS.question');
        $questions = SurveySectionValue::select(['id', 'survey_section_id', 'question_id', 'group_id'])->where('survey_section_id', $surveyID)->where('type', $questionTypeID)->get();
        $SCL90GroupQuestionCount = $questions->groupBy('group_id')->map->count();
        $symptomGroups = SymptomSurveyGroup::all()->map(function ($group) {
            return [
                'group_id' => $group->id,
                'group_title' => $group->title,
            ];
        });

        $surveyAnswers = collect();

        // Set all given question values
        foreach ($questions as $key => $question) {
            if (!array_key_exists($question->question_id, $data)) {
                continue;
            }

            $temp = [];
            $temp['value'] = $data[$question->question_id]; //  0,1,2,3,4
            $temp['id'] = $question->question_id;
            $temp['group_id'] = $question->group_id;
            $surveyAnswers->push($temp);
        }

        // current SCL90  groups results for the sum
        $groupedAnswers = $surveyAnswers->groupBy('group_id');

        $results = $symptomGroups->map(function ($symptomGroup) use ($SCL90GroupQuestionCount, $groupedAnswers, $surveyID) {
            $result = null;
            $groupID = $symptomGroup['group_id'];

            if ($groupedAnswers->offsetExists($groupID)) {
                $result = SurveyHelper::calculateSCL90GroupResult($groupedAnswers, $SCL90GroupQuestionCount, $groupID);
            }

            return array_merge($symptomGroup, ['result' => $result, 'resultLevel' => Survey::getResultLevel($result, $surveyID)]);
        });

        return response()->json(['code' => 1, 'message' => 'success', 'data' => $results->prepend(Survey::getSCL90GST($surveyAnswers, $surveyID))]);
    }

    public function getResults()
    {
    }

    // public function test()
    // {
    //     $data = json_decode('{ "surveyID": 7, "data": { "11": { "1": 2, "2": 2, "3": 0, "4": 0, "5": 0, "6": 0 }, "12": { "7": 0, "8": 0, "9": 0 }, "13": { "1": 0, "2": 0, "3": 0, "4": 0, "5": 0, "6": 0 }, "14": { "7": 0, "8": 0, "9": 0 } } }', true);
    //     $results = collect([]);
    //     $data = $data['data'];
    //     $keys = ['P' => collect($data[11]), 'C' => collect($data[13]), 'P7_9' => collect($data[12]), 'C7_9' => collect($data[14])];

    //     foreach ($keys as $itq => $data) {
    //         if (strlen($itq) != 1) {
    //             $results[$itq] = Survey::ITQResultModel($data->values(), $itq, false);
    //             continue;
    //         }

    //         for ($i = 1; $i < count($data); $i += 2) {
    //             $values = collect([$data[$i], $data[$i + 1]]);
    //             $key = $itq . $i . '_' . $i + 1;
    //             $results[$key] = Survey::ITQResultModel($values, $key);
    //         }
    //     }

    //     $hasKPTSA = $results->every(config('constants.meetsAllITQCriterias'));
    //     $hasPTSA = Survey::meetsITQCriterias($results, 'P');
    //     $hasTOD = Survey::meetsITQCriterias($results, 'C');

    //     $results['KPTSA'] = ['group_title' => 'KPTSA', 'result' => $hasKPTSA];
    //     $results['PTSA']  = ['group_title' => 'PTSA', 'sum' => Survey::filterByITQKey($results, 'P')->pluck('sum')->sum(), 'result' => $hasPTSA && !$hasTOD];
    //     $results['TOD']  = ['group_title' => 'TOD', 'sum' => Survey::filterByITQKey($results, 'C')->pluck('sum')->sum(), 'resu;t' => $hasTOD];

    //     return response()->json($results->values());
    // }
    // public function test()
    // {
    //     $map =   [
    //         "6 თვეზე ნაკლები ხნის წინ",
    //         "6-12 თვის წინ",
    //         "1-5 წლის წინ",
    //         "5-10 წლის წინ",
    //         "10-20 წლის წინ",
    //         "20 წელზე მეტი ხნის წინ"
    //     ];
    //     $temp = [];

    //     foreach ($map as $key => $item) {
    //         $temp[] = ['id' => $key + 1, 'value' => trim($item), 'text' => trim($item)];
    //     }
    //     return $temp;

    //     // {
    //     //     SurveyDefinitionValue::whereIn('id', [1, 4, 12, 27, 40, 42, 48, 49, 52, 53, 56, 58])->update(['group_id' => 1]);
    //     //     SurveyDefinitionValue::whereIn('id', [3, 9, 10, 28, 38, 45, 46, 51, 55, 65])->update(['group_id' => 2]);
    //     //     SurveyDefinitionValue::whereIn('id', [6, 21, 34, 36, 37, 41, 61, 69, 73])->update(['group_id' => 3]);
    //     //     SurveyDefinitionValue::whereIn('id', [5, 14, 15, 20, 22, 26, 29, 30, 31, 32, 54, 71, 79])->update(['group_id' => 4]);
    //     //     SurveyDefinitionValue::whereIn('id', [2, 17, 23, 33, 39, 57, 72, 78, 80, 86])->update(['group_id' => 5]);
    //     //     SurveyDefinitionValue::whereIn('id', [11, 24, 63, 67, 74, 81])->update(['group_id' => 6]);
    //     //     SurveyDefinitionValue::whereIn('id', [13, 25, 47, 50, 70, 75, 82])->update(['group_id' => 7]);
    //     //     SurveyDefinitionValue::whereIn('id', [8, 18, 43, 68, 76, 83])->update(['group_id' => 8]);
    //     //     SurveyDefinitionValue::whereIn('id', [7, 16, 35, 62, 77, 84, 85, 87, 88, 90])->update(['group_id' => 9]);
    //     //     SurveyDefinitionValue::whereIn('id', [19, 60, 44, 59, 64, 66, 89])->update(['group_id' => 10]);

    //     // $ids = Attr::pluck('id');
    //     // $valueIDS = AttrValue::pluck('id');
    //     // $propIDS = AttrProperty::pluck('id');

    //     // $attr =  Attr::whereIn('id', $ids)->update(['status_id' => 1]);
    //     // $value = AttrValue::get()->update(['status_id' => 1]);
    //     // $property = AttrProperty::get()->update(['status_id' => 1]);

    //     // return [
    //     //     'attr' => $attr,
    //     //     'values' => $value,
    //     //     'property' => $property
    //     // ];
    // }
}
