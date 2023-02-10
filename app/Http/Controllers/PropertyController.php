<?php

namespace App\Http\Controllers;

use App\Models\AttrProperty;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class PropertyController extends Controller
{
    public function addProperty(Request $request)
    {
        $data = $request->only([
            'p_id',
            'source_attr_id',
            'title',
            'input_data_type',
            'input_view_type',
            'is_mandatory',
            'has_filter',
            'is_primary',
            'attr_id',
            'type',
            'order_id',
        ]);

        $lastPropertyOrderID = AttrProperty::where('attr_id', $data['attr_id'])->where('type', 1)->max('order_id') + 1;

        $data['order_id'] = $lastPropertyOrderID;

        $validator = Validator::make($data, [
            'p_id' => 'required',
            'source_attr_id' => 'required',
            'title' => 'required',
            'has_filter' => 'required',
            'is_primary' => 'required',
            'attr_id' => 'required',
            'type' => 'required',
            'order_id' => 'required',
            'input_data_type' => 'required',
            'input_view_type' => 'required',
            'is_mandatory' => 'required',
        ]);

        if ($validator->fails()) {
            return $validator->errors();
        }

        $property = AttrProperty::create($data);

        if (!$property) {
            return response()->json(['code' => 0, 'message' => 'დაფიქსირდა შეცდომა'], 400);
        }

        if ($property->is_primary) {
            AttrProperty::where('id', '!=', $property->id)->where('p_id', $property->p_id)->where('attr_id', $property->attr_id)->update(['is_primary' => 0]);
        }

        $data = AttrProperty::where('attr_id', $property->attr_id)->get();

        return response()->json(['code' => 1, 'message' => 'ოპერაცია წარმატებით დასრულდა', 'data' => $data]);
    }

    public function addSection(Request $request)
    {
        $data = $request->only([
            'attr_id',
            'title',
        ]);

        $validator = Validator::make($data, [
            'attr_id' => 'required',
            'title' => 'required',
        ]);

        if ($validator->fails()) {
            return $validator->errors();
        }

        $section = [
            'p_id' => 0,
            'attr_id' => intval($data['attr_id']),
            'title' => $data['title'],
            'input_data_type' => 1,
            'input_view_type' => 1,
            'is_mandatory' => 1,
            'has_filter' => 1,
            'is_primary' => 0,
            'type' => 2,
            'order_id' => AttrProperty::where('attr_id', intval($data['attr_id']))->where('type', 2)->max('order_id') + 1,
        ];

        $property = AttrProperty::create($section);

        if (!$property) {
            return response()->json(['code' => 0, 'message' => 'დაფიქსირდა შეცდომა'], 400);
        }

        $data = AttrProperty::where('attr_id', $property->attr_id)->get();

        return response()->json(['code' => 1, 'message' => 'სექცია წარმატებით დაემატა', 'data' => $data]);
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

        if ($property->is_primary) {
            AttrProperty::where('id', '!=', $propertyID)->where('attr_id', $property->attr_id)->update(['is_primary' => 0]);
        }

        return response()->json([
            'code' => 1,
            'message' => 'ოპერაცია წარმატებით დასრულდა',
            'data' => $property
        ]);
    }

    public function reorderProperties(Request $request)
    {
        $values = $request->all();
        $orderID = 1;
        foreach ($values as $val) {
            $propertyID = intVal($val);
            $property = AttrProperty::find($propertyID);
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
