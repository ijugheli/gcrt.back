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

    public function attrs()
    {
        $attrs = Attr::with(['properties'])->get();

        foreach ($attrs as $key => $attr) {
            if (!$attr->hasOptions())
                continue;

            $attrs[$key]['values'] = AttrValue::where('p_value_id', 0)->where('attr_id', $attr->id)->get();;
        }

        return response()->json($attrs);
    }


    /**
     * Returns tree nodes for current request;
     *
     * @return void
     */
    public function treeNodes()
    {
        $attrID = request()->attr_id;
        $valueID = request()->value_id;
        $values = AttrValue::where('p_value_id', $valueID)->where('attr_id', $attrID)->get();

        return $this->asNodes($values);
    }
    /**
     * Undocumented function
     *
     * @param [type] $attribute
     * @return void
     */
    private function lazyTree($attribute = NULL)
    {
        if (is_null($attribute)) {
            return false;
        }

        $attribute['properties'] = AttrProperty::where('attr_id', $attribute->id)->get();
        $attribute['values'] = AttrValue::where('p_value_id', 0)->where('attr_id', $attribute->id)->get();
        $attribute['tree'] = $this->asNodes($attribute['values']);

        return $attribute;
    }
    /**
     * Endpoint for table
     *
     * @return void
     */
    public function table()
    {
        $attribute = Attr::find(request()->attr_id);

        if (is_null($attribute)) {
            return response()->json(['StatusMessage' => 'მონაცემები ვერ მოიძებნა'], 400);
        }

        if ($attribute->isTree && $attribute->lazy) {
            return $this->lazyTree($attribute);
        }

        $attribute = Attr::with(['properties', 'values'])->find(request()->attr_id);
        $attribute = $this->withChildren($attribute);
        $attribute = $this->withSources($attribute);

        if ($attribute->isTree) {
            $attribute['tree'] = $this->asValueTree($attribute->values);
            return $attribute;
        }

        $attribute = $this->withRows($attribute);

        return $attribute;
    }

    /**
     * Filters attribute values by valueID
     *
     * @param [type] $attribute
     * @param [type] $valueID
     * @return Attr
     */
    private function onlyRelated($attribute = NULL, $valueID = NULL)
    {
        if (is_null($attribute)) {
            return false;
        }

        $values = [];
        foreach ($attribute->values as $key => $value) {
            $currentValueID = intval($attribute->values[$key]['value_id']);
            $relatedValueID = intval($attribute->values[$key]['related_value_id']);
            if ($relatedValueID == $valueID) {
                $values[$currentValueID] = $value;
            }
        }
        $attribute['values'] = $values;

        return $attribute;
    }
    /**
     * Undocumented function
     *
     * @return void
     */
    public function relatedTable()
    {
        $attrID = request()->attr_id;
        $valueID = request()->valueID;

        if (is_null($attrID) || is_null($valueID)) {
            return false;
        }

        $attribute = Attr::with(['properties', 'values'])->where('attr_id', $attrID)->get();
        $attribute = $this->onlyRelated($attribute, $valueID);

        if ($attribute->isTree) {
            $attribute['tree'] = $this->asValueTree($attribute->values);
            return $attribute;
        }

        $attribute = $this->withChildren($attribute);
        $attribute = $this->withSources($attribute);
        $attribute = $this->withRows($attribute);

        return $attribute;
    }


    /**
     * Transfors list of values to node objects
     *
     * @param [type] $values
     * @return void
     */
    private function asNodes($values = NULL)
    {
        if (is_null($values)) {
            return false;
        }

        $response = [];

        foreach ($values as $value) {
            if (isset($response[$value['value_id']])) {
                $response[$value['value_id']]['data'][$value->property_id] = $value->value;
                continue;
            }

            $node = $value->getNode(true);
            $response[$value['value_id']] = $node;
        }

        return array_values($response);
    }



    /**
     * Appends children attributes to attribute;
     *
     * @param [Attr] $attribute
     * @return Attr
     */
    private function withChildren($attribute = NULL)
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


    /**
     * Tranforms to value tree.
     *
     * @param [type] $values
     * @param integer $parentID
     * @return void
     */
    private function asValueTree($values = NULL, $parentID = 0)
    {
        if (is_null($values)) {
            return false;
        }

        $recursive = [];

        foreach ($values as $key => $value) {
            if ($value['p_value_id'] != $parentID) {
                continue;
            }

            $valueID = intval($value['value_id']);

            if (isset($recursive[$valueID])) {
                $recursive[$valueID]['data'][$value['property_id']] = $value->value;
                continue;
            }

            $recursive[$valueID] = $value->getNode(true);
            $recursive[$valueID]['children'] = $this->asValueTree($values, $valueID);
        }

        return $recursive;
    }


    /**
     * Appends full soruce attributes.
     *
     * @param [type] $attribute
     * @return Attr
     */
    private function withSources($attribute = NULL)
    {
        if (is_null($attribute)) {
            return false;
        }

        foreach ($attribute->properties as $key => $property) {
            $sourceID = $property->source_attr_id;

            if (is_null($sourceID)) {
                continue;
            }

            $source = Attr::with(['properties', 'values'])->where('id', $sourceID)->first();
            $attribute->properties[$key]['sourceAttribute'] = $source;

            //Processing source attribute tree.
            // if ($source->isTree && ) {
            //     return ;
            // }

            if ($source->isTree) {
                $attribute->properties[$key]['tree'] = $source->lazy
                    ? $this->lazyTree($source)['tree']
                    : $this->asValueTree($source->values);
            }

            $attribute->properties[$key]['source'] =
                AttrValue::where('attr_id',  $sourceID)
                ->where('property_id', $this->primaryPropertyID($sourceID))
                ->get();
        }

        return $attribute;
    }

    /**
     * Transfors value list to {propID => value, propID => value}
     *
     * @param [type] $attribute
     * @return void
     */
    private function withRows($attribute = NULL)
    {
        if (is_null($attribute)) {
            return false;
        }

        $values = [];
        foreach ($attribute->values as $key => $value) {
            $valueID = intval($attribute->values[$key]['value_id']);
            $value['property_id'] = intval($value['property_id']);

            if (!isset($values[$valueID])) {
                $values[$valueID] = [
                    'valueID' => $valueID,
                    'id' => $value['id']
                ];
            }

            $values[$valueID][$value['property_id']] = $value['value'];
        }

        $attribute['rows'] = array_values($values);

        return $attribute;
    }

    /**
     * Used for add form
     *
     * @return void
     */
    public function properties()
    {
        $attrID = request()->attr_id;
        $attribute = Attr::with(['properties'])->find($attrID);
        $attribute = $this->withSources($attribute);
        $attribute = $this->withChildren($attribute);

        return $attribute;
    }

    /**
     * Used for Edit Form.
     *
     * @return void
     */
    public function value()
    {
        $attrID = request()->attr_id;
        $valueID = request()->value_id;
        $attribute = Attr::with(['properties'])->find($attrID);
        $attribute = $this->withChildren($attribute);
        $attribute = $this->withSources($attribute);

        if ($attribute->isTree) {
            $attribute['values'] = $this->asValueTree($attribute->values);
        }

        $values = AttrValue::where('attr_id', $attrID)->where('value_id', $valueID)->get();

        $valueMap = [];

        foreach ($values as $key => $value) {
            $valueMap[$value->property_id] = $value;
        }

        $attribute['values'] = $valueMap;

        return $attribute;
    }

    public function list()
    {
        return Attr::with(['properties'])->get();
    }

    public function values()
    {
        return Attr::find(request()->attr_id)->values;
    }

    public function addRecord(Request $request)
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

    public function editRecord(Request $request)
    {
        $values = $request->all();
        $attrID = intval($request->route('attr_id'));
        $valueID = intval($request->route('value_id'));

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
        $attribute = Attr::where('id', $attrID)->first();
        $values = $request->all();
        $valueIDs = array_map('intval', $values);

        AttrValue::where('attr_id', $attrID)->whereIn('value_id', $valueIDs)->delete();

        if ($attribute->isTree) {
            AttrValue::where('attr_id', $attrID)->whereIn('p_value_id', $valueIDs)->delete();
        }
    }

    public function setTitle(Request $request)
    {
        $attrID = intval(request()->attr_id);
        $data = $request->only([
            'title',
        ]);

        $validator = Validator::make($data, [
            'title' => 'required',
        ]);

        if ($validator->fails()) {
            return $validator->errors();
        }


        $attribute = Attr::find($attrID);

        if ($attribute == null) {
            return response()->json([
                'მონაცემები ვერ მოიძებნა'
            ], 400);
        }

        $attribute->title = $data['title'];
        $attribute->save();

        return response()->json(['StatusMessage' => 'ოპერაცია წარმატებით დასრულდა']);
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
}
