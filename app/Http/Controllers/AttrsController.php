<?php

namespace App\Http\Controllers;

use App\Models\Admins;
use App\Models\Attr;
use App\Models\AttrProperty;
use App\Models\AttrValue;
use Illuminate\Http\Request;
use DateTime;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class AttrsController extends Controller
{

    public function list()
    {
        $attrs = Attr::with(['properties'])->get();
        foreach ($attrs as $attr) {
            $result = DB::select('SELECT COUNT(0) as count 
                                    FROM (SELECT 0 FROM `attr_values` WHERE attr_id = ? GROUP BY value_id) a', [$attr->id]);
            $attr['count'] = $result[0]->count;
        }

        return $attrs;
    }

    public function values()
    {
        return Attr::find(request()->attr_id)->values;
    }

    public function addValues(Request $request)
    {
        $values = $request->all();
        $attrID = intval($request->route('attr_id'));
        $valueID = AttrValue::where('attr_id', $attrID)->max('value_id');
        $valueID = is_null($valueID) ? 1 : $valueID + 1;
        $sanitizedValues = [];

        foreach ($values as $entry) {
            $propertyID = $entry[0];
            $value = $entry[1];

            if (is_null($value)) {
                continue;
            }

            $value['value_id'] = $valueID;
            $value['attr_id'] = $attrID;

            if (!is_null($value['value_date'])) {
                $value['value_date'] = (new DateTime($value['value_date']))->format('Y-m-d h:m:s');
            }

            unset($value['id']);
            unset($value['insert_date']);
            unset($value['update_date']);
            array_push($sanitizedValues, $value);
        }

        Attr::find($attrID)->values()->createMany($sanitizedValues);

        return response()->json(['ოპერაცია წარმატებით დასრულდა']);
    }

    public function editValues(Request $request)
    {
        $values = $request->all();
        $attrID = intval($request->route('attr_id'));
        $valueID = intval($request->route('value_id'));
        $sanitizedValues = [];

        foreach ($values as $entry) {
            $propertyID = $entry[0];
            $newValue = $entry[1];
            $value = AttrValue::where('attr_id', $attrID)->where('property_id', $propertyID)
                ->where('value_id', $valueID)->first();

            if (is_null($value)) {
                continue;
            }

            if ($value['valueName'] == 'value_date' && !is_null($newValue['value_date'])) {
                $newValue['value_date'] = (new DateTime($newValue['value_date']))->format('Y-m-d h:m:s');
            }

            if ($value['valueName'] == 'value_boolean') {
                $newValue['value_boolean'] = isset($newValue['value_boolean']) && $newValue['value_boolean'] == 1 ? 1 : 0;
            }

            $value->update([$value['valueName'] => $newValue[$value['valueName']]]);
            $value->save();
        }

        return response()->json(['ოპერაცია წარმატებით დასრულდა']);
    }




























    public function editValue(Request $request)
    {
        $data = $request->only([
            'value_id',
            'attr_id',
            'property_id',
            'value',
        ]);

        $validator = Validator::make($data, [
            'value_id' => 'required',
            'attr_id' => 'required',
            'value' => 'required',
            'property_id' => 'required',
        ]);

        if ($validator->fails()) {
            return $validator->errors();
        }

        $value = AttrValue::where('value_id', $request->value_id)
            ->where('property_id', $request->property_id)
            ->where('attr_id', $request->attr_id)->first();

        if ($value == null) {
            return response()->json([
                'მონაცემები ვერ მოიძებნა'
            ], 400);
        }

        $valueName = $value->valueName;
        $value->$valueName = $data['value'];

        if ($value->save()) {
            return response()->json(['ოპერაცია წარმატებით დასრულდა']);
        }

        return response()->json(['ოპერაციის შესრულების დროს მოხდა შეცდომა'], 500);
    }

    public function remove(Request $request)
    {
        $attrID = intval(request()->attr_id);
        $values = $request->all();
        $valueIDs = array_map('intval', $values);

        AttrValue::where('attr_id', $attrID)->whereIn('value_id', $valueIDs)->delete();
    }

    public function withProperties()
    {
        $attrID = request()->attr_id;
        $attribute = Attr::with(['properties'])->find($attrID);
        $attribute = $this->appendSourceAttrs($attribute);
        $attribute = $this->appendChildren($attribute);


        return $attribute;
    }

    public function withPropertyValues()
    {
        $attrID = request()->attr_id;
        $valueID = request()->value_id;
        $attribute = Attr::with(['properties'])->find($attrID);
        $attribute = $this->appendSourceAttrs($attribute);
        $attribute = $this->appendChildren($attribute);

        if ($attribute->type == config('settings.ATTR_TYPES')['tree']) {
            // $attribute = $this->processTree($attribute);
        }

        $values = AttrValue::where('attr_id', $attrID)->where('value_id', $valueID)->get();

        $valueMap = [];

        foreach ($values as $key => $value) {
            $valueMap[$value->property_id] = $value;
        }

        $attribute['values'] = $valueMap;

        return $attribute;
    }







    private function primaryPropertyID($attrID = NULL)
    {
        if (is_null($attrID)) {
            return false;
        }

        $property = AttrProperty::where('attr_id', $attrID)->where('is_primary', 1)->first();
        //@
        if (is_null($property)) {
            return response()->json([$attrID, 'source has no primary title']);
        }
        return $property->id;
    }









    public function related()
    {
        $attrID = request()->attr_id;
        $valueID = request()->valueID;
        if (is_null($attrID) || is_null($valueID)) {
            return false;
        }

        $attribute = Attr::with(['properties', 'values'])->where('attr_id', $attrID)->get();

        $values = [];
        foreach ($attribute->values as $key => $value) {
            $currentValueID = intval($attribute->values[$key]['value_id']);
            $relatedValueID = intval($attribute->values[$key]['related_value_id']);
            if ($relatedValueID == $valueID) {
                $values[$currentValueID] = $value;
            }
        }

        $attribute = $this->appendSourceAttrs($attribute);
        $attribute = $this->appendChildren($attribute);
        if ($attribute->type == config('settings.ATTR_TYPES')['tree']) {
            $attribute['tree'] = $this->processTree($attribute);
        } else {
            $attribute = $this->appendSourceAttrs($attribute);
            $attribute['rows'] = $this->getRows($attribute);
        }

        return $attribute;
    }



    public function full()
    {
        $attribute = Attr::with(['properties', 'values'])->find(request()->attr_id);

        if (is_null($attribute)) {
            return response()->json(['StatusMessage' => 'მონაცემები ვერ მოიძებნა'], 400);
        }

        $attribute = $this->appendSourceAttrs($attribute);
        $attribute = $this->appendChildren($attribute);
        if ($attribute->type == config('settings.ATTR_TYPES')['tree']) {
            $attribute['tree'] = $this->processTree($attribute);
        } else {
            $attribute = $this->appendSourceAttrs($attribute);
            $attribute['rows'] = $this->getRows($attribute);
        }

        return $attribute;
    }

    private function appendChildren($attribute = NULL)
    {
        if (is_null($attribute)) {
            return false;
        }

        $children = Attr::with(['properties', 'values'])->where('p_id', $attribute->id)->get();
        if (is_null($children) || empty($children)) {
            return $attribute;
        }

        $attribute['children'] = $children;

        return $attribute;
    }

    private function processTree($attribute = NULL)
    {
        if (is_null($attribute)) {
            return false;
        }

        $values = [];

        foreach ($attribute->values as $key => $value) {
            $valueID = intval($attribute->values[$key]['value_id']);
            $value['property_id'] = intval($value['property_id']);
            $parentID = $value['p_value_id'];
            $hasParent = (!is_null($parentID) && $parentID > 0);
            $parentExists = isset($values[$parentID]);

            if ($hasParent) {
                continue;
            }

            if (!isset($values[$valueID])) {
                $values[$valueID] = [
                    'data' => ['value_id' => $value['value_id']],
                    'label' => '',
                    'children' => []
                ];
            }

            $values[$valueID]['data'][$value['property_id']] = $value['value'];
            $values[$valueID]['label'] .= $value['value'] . '  ';
        }

        foreach ($attribute->values as $key => $value) {
            $valueID = intval($attribute->values[$key]['value_id']);
            $value['property_id'] = intval($value['property_id']);
            $parentID = $value['p_value_id'];
            $hasParent = (!is_null($parentID) && $parentID > 0);
            $parentExists = isset($values[$parentID]);

            if (!$hasParent) {
                continue;
            }

            if (!isset($values[$parentID]['children'][$valueID])) {
                $values[$parentID]['children'][$valueID] = [
                    'data' => ['value_id' => $value['value_id']],
                    'label' => '',
                    'children' => []
                ];
            }

            $values[$parentID]['children'][$valueID]['data'][$value['property_id']] = $value['value'];
            $values[$parentID]['children'][$valueID]['label'] .= $value['value'] . '  ';
        }

        // foreach ($attribute->values as $key => $value) {
        //     $valueID = intval($attribute->values[$key]['value_id']);
        //     $value['property_id'] = intval($value['property_id']);
        //     $parentID = $value['p_value_id'];
        //     $hasParent = (!is_null($parentID) && $parentID > 0);
        //     $parentExists = isset($values[$parentID]);
        //     // $objectExists = isset($values[$valueID]);

        //     if ($hasParent) {
        //         if (!$parentExists) {
        //             $values[$parentID] = [
        //                 'data' => [],
        //                 'children' => []
        //             ];
        //         }

        //         if (!isset($values[$parentID]['children'][$valueID])) {
        //             $values[$parentID]['children'][$valueID] = [
        //                 'data' => [],
        //                 'children' => []
        //             ];
        //         }

        //         $values[$parentID]['children'][$valueID]['data'][$value['property_id']] = $value['value'];
        //     } else {
        //         if (!isset($values[$valueID])) {
        //             $values[$valueID] = [
        //                 'data' => [
        //                     ''
        //                 ],
        //                 'children' => []
        //             ];
        //         }

        //         $values[$valueID]['data'][$value['property_id']] = $value['value'];
        //     }
        // }

        return $values;
    }

    private function appendSourceAttrs($attribute)
    {
        if (is_null($attribute)) {
            return false;
        }

        foreach ($attribute->properties as $key => $property) {
            $sourceAttrID = $attribute->properties[$key]['source_attr_id'];

            if (is_null($sourceAttrID)) {
                continue;
            }

            $sourceAttr = Attr::with(['properties', 'values'])->where('id', $sourceAttrID)->first();
            $attribute->properties[$key]['sourceAttribute'] = $sourceAttr;

            if ($sourceAttr->type == config('settings.ATTR_TYPES')['tree']) {
                $attribute->properties[$key]['tree'] = $this->processTree($sourceAttr);
            }

            $attribute->properties[$key]['source'] =
                AttrValue::where('attr_id',  $sourceAttrID)->where('property_id', $this->primaryPropertyID($sourceAttrID))->get();
        }

        return $attribute;
    }

    private function getRows($attribute)
    {
        if (is_null($attribute)) {
            return false;
        }

        $values = [];
        foreach ($attribute->values as $key => $value) {
            $valueID = intval($attribute->values[$key]['value_id']);
            $value['property_id'] = intval($value['property_id']);
            // $value['property'] = $attribute->properties[$value['property_id']];

            if (!isset($values[$valueID])) {
                $values[$valueID] = [
                    'valueID' => $valueID,
                    'id' => $value['id']
                ];
            }

            $values[$valueID][$value['property_id']] = $value['value'];
        }

        return array_values($values);
    }
}
