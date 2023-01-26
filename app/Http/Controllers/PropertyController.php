<?php

namespace App\Http\Controllers;

use App\Models\Attr;
use App\Models\AttrValue;
use App\Models\AttrProperty;
use Illuminate\Http\Request;


class PropertyController extends Controller
{
    public function addProperty(Request $request)
    {

        $values = $request->all();
        $attrID = intval($request->route('attr_id'));
        $lastPropertyOrderID = AttrProperty::where('attr_id', $attrID)->get()->max('order_id');
        $propertyData = [];

        $propertyData['source_attr_id']    = intVal($values['source']['id']);
        $propertyData['title']             = $values['title'];
        $propertyData['input_data_type']   = intVal($values['data_type']['id']);
        $propertyData['input_view_type']   = intVal($values['data_view']['id']);
        $propertyData['is_mandatory']      = intVal($values['is_mandatory']);
        $propertyData['has_filter']        = intVal($values['has_filter']);
        // $propertyData['is_primary']        = intVal($values['is_primary']);
        $propertyData['attr_id']           = intVal($values['parent_id']);
        $propertyData['type']              = intVal($values['type']['id']);
        $propertyData['order_id']          = intVal($lastPropertyOrderID) + 1;


        return AttrProperty::create($propertyData);
    }

    public function removeProperty(Request $request)
    {
        $attrID = intval(request()->attr_id);

        $propertyID = $request['property_id'];
        $property = AttrProperty::where('id', $propertyID)->first();
    }

    public function updateProperty(Request $request)
    {
        $propertyID = $request->route('property_id');
        $data = $request->data;
        $property = AttrProperty::find($propertyID);

        if (is_null($property)) {
            return response()->json([
                'code' => 0,
                'message' => 'ატრიბუტი ვერ მოიძებნა'
            ]);
        }

        $property->update($data);

        return response()->json([
            'code' => 1,
            'message' => 'ოპერაცია წარმატებით დასრულდა',
            'data' => $property
        ]);
        // $property->update($data);

    }

    public function reorderProperties(Request $request)
    {
        $values = $request->all();
        $orderID = 1;
        foreach ($values as $val) {
            // print_r($val);
            $propertyID = intVal($val);
            $property = AttrProperty::find($propertyID);
            // print_r($property);
            $property['order_id'] = $orderID;
            $property->save();
            $orderID++;
        }

        return response()->json([
            'code' => 1,
            'message' => 'ოპერაცია წარმატებით დასრულდა'
        ]);
    }
}
