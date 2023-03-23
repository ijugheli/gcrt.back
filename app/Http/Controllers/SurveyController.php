<?php

namespace App\Http\Controllers;

use App\Models\Survey;
use App\Http\Helpers\Helper;
use Illuminate\Http\Request;
use App\Models\SurveySection;
use App\Http\Helpers\SurveyHelper;
use App\Http\Resources\SurveyResource;
use App\Models\SurveySectionValue;
use App\Models\SymptomSurveyGroup;
use App\Http\Resources\SurveySectionResource;

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
        $surveySections = SurveySection::createAndMapSections($data['definitions'], $survey->id);

        foreach ($surveySections as $section) {
            $typeIDS  = config('constants.surveySectionValueTypeIDS');

            if ($section['questions']->isNotEmpty() && $section['section']->isMatrix()) {
                $this->createSurveySectionValues($section['questions'], $typeIDS['question'], $section['section']->id);
            }

            if ($section['choices']->isNotEmpty()) {
                $this->createSurveySectionValues($section['choices'], $typeIDS['choice'], $section['section']->id);
            }
        }
    }

    public function getSurvey()
    {
        $survey = Survey::find(request()->route('survey_id'));

        if (is_null($survey)) {
            return response()->json([
                'code' => 0,
                'message', 'კითხვარი ვერ მოიძებნა'
            ], 400);
        }

        return response()->json([
            'code' => 1,
            'message' => 'success',
            'data' => SurveyResource::make($survey)
        ]);
    }

    public function list()
    {
        return response()->json([
            'code' => 1,
            'message' => 'success',
            'data' => Survey::select(['id', 'title'])->get()
        ]);
    }

    public function store(Request $request)
    {
        $data = collect($request->all());

        return $this->SCL90Handler($data);
    }

    private function SCL90Handler($data)
    {
        $data = $data->first();
        $questionTypeID = config('constants.surveySectionValueTypeIDS.question');;
        $questions = SurveySectionValue::select(['id', 'survey_section_id', 'question_id', 'group_id'])->where('survey_section_id', 1)->where('type', $questionTypeID)->get();
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

            $array = [];
            $array['value'] = $data[$question->question_id]; //  0,1,2,3,4
            $array['id'] = $question->question_id;
            $array['group_id'] = $question->group_id;
            $surveyAnswers->push($array);
        }

        // SCL90  groups for result scale
        $groupedAnswers = $surveyAnswers->groupBy('group_id');

        $results = $symptomGroups->map(function ($symptomGroup) use ($SCL90GroupQuestionCount, $groupedAnswers) {
            $result = null;
            $groupID = $symptomGroup['group_id'];

            if ($groupedAnswers->offsetExists($groupID)) {
                $result = SurveyHelper::calculateSCL90GroupResult($groupedAnswers, $SCL90GroupQuestionCount, $groupID);
            }

            return array_merge($symptomGroup, ['result' => $result, 'resultLevel' => Survey::getSCL90ResultLevel($result)]);
        });

        return response()->json(['code' => 1, 'message' => 'success', 'data' => $results->prepend(Survey::getSCL90GST($surveyAnswers))]);
    }

    public function getResults()
    {
    }

    private function createSurveySectionValues($data, $type, $surveySectionID)
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

    public function test()
    {
        $map =   [
            "6 თვეზე ნაკლები ხნის წინ",
            "6-12 თვის წინ",
            "1-5 წლის წინ",
            "5-10 წლის წინ",
            "10-20 წლის წინ",
            "20 წელზე მეტი ხნის წინ"
        ];
        $temp = [];

        foreach ($map as $key => $item) {
            $temp[] = ['id' => $key + 1, 'value' => trim($item), 'text' => trim($item)];
        }
        return $temp;

        // {
        //     SurveyDefinitionValue::whereIn('id', [1, 4, 12, 27, 40, 42, 48, 49, 52, 53, 56, 58])->update(['group_id' => 1]);
        //     SurveyDefinitionValue::whereIn('id', [3, 9, 10, 28, 38, 45, 46, 51, 55, 65])->update(['group_id' => 2]);
        //     SurveyDefinitionValue::whereIn('id', [6, 21, 34, 36, 37, 41, 61, 69, 73])->update(['group_id' => 3]);
        //     SurveyDefinitionValue::whereIn('id', [5, 14, 15, 20, 22, 26, 29, 30, 31, 32, 54, 71, 79])->update(['group_id' => 4]);
        //     SurveyDefinitionValue::whereIn('id', [2, 17, 23, 33, 39, 57, 72, 78, 80, 86])->update(['group_id' => 5]);
        //     SurveyDefinitionValue::whereIn('id', [11, 24, 63, 67, 74, 81])->update(['group_id' => 6]);
        //     SurveyDefinitionValue::whereIn('id', [13, 25, 47, 50, 70, 75, 82])->update(['group_id' => 7]);
        //     SurveyDefinitionValue::whereIn('id', [8, 18, 43, 68, 76, 83])->update(['group_id' => 8]);
        //     SurveyDefinitionValue::whereIn('id', [7, 16, 35, 62, 77, 84, 85, 87, 88, 90])->update(['group_id' => 9]);
        //     SurveyDefinitionValue::whereIn('id', [19, 60, 44, 59, 64, 66, 89])->update(['group_id' => 10]);

        // $ids = Attr::pluck('id');
        // $valueIDS = AttrValue::pluck('id');
        // $propIDS = AttrProperty::pluck('id');

        // $attr =  Attr::whereIn('id', $ids)->update(['status_id' => 1]);
        // $value = AttrValue::get()->update(['status_id' => 1]);
        // $property = AttrProperty::get()->update(['status_id' => 1]);

        // return [
        //     'attr' => $attr,
        //     'values' => $value,
        //     'property' => $property
        // ];
    }
}
