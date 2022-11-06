<?php

namespace App\Http\Controllers;

use App\Models\Admins;
use App\Models\Attr;
use App\Models\AttrProperty;
use App\Models\AttrValue;
use Illuminate\Http\Request;
use DateTime;


class AttrsController extends Controller
{

    public function list()
    {
        return Attr::with(['properties'])->get();
    }

    public function details()
    {
        $attribute = Attr::with(['properties'])->find(request()->attr_id);

        foreach ($attribute->properties as $key => $property) {
            $sourceAttrID = $attribute->properties[$key]['source_attr_id'];

            if (is_null($sourceAttrID)) {
                continue;
            }

            $attribute->properties[$key]['source'] = AttrValue::where('attr_id',  $sourceAttrID)->get();
        }

        return $attribute;
    }

    public function full()
    {
        $attribute = Attr::with(['properties', 'values'])->find(request()->attr_id);

        foreach ($attribute->properties as $key => $property) {
            $sourceAttrID = $attribute->properties[$key]['source_attr_id'];

            if (is_null($sourceAttrID)) {
                continue;
            }

            $attribute->properties[$key]['source'] = AttrValue::where('attr_id',  $sourceAttrID)->get();
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

        $attribute['rows'] = array_values($values);

        return $attribute;
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
    }

    public function values()
    {
        return Attr::find(request()->attr_id)->values;
    }

    public function add()
    {
        echo request()->options['c']['k'];
    }

    public function remove(Request $request)
    {
        $attrID = intval(request()->attr_id);
        $values = $request->all();
        $valueIDs = array_map('intval', $values);

        AttrValue::where('attr_id', $attrID)->whereIn('value_id', $valueIDs)->delete();
    }
}
