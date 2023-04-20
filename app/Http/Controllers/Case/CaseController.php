<?php

namespace App\Http\Controllers\Case;

use Illuminate\Http\Request;
use App\Models\Case\CaseModel;
use App\Http\Controllers\Controller;
use App\Http\Resources\CaseResource;
use App\Http\Services\Case\CaseService;

class CaseController extends Controller implements CaseControllerInterface
{
    private $service;

    public function __construct(CaseService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        return response()->json([
            'code' => 1, 'message' => 'Success', 'data' => CaseResource::collection($this->service->index())
        ]);
    }

    public function show($id)
    {
        $data = $this->service->show($id);

        if (is_null($data)) {
            return response()->json([
                'code' => 0, 'message' => 'ჩანაწერი ვერ მოიძებნა',
            ], 400);
        }

        return response()->json([
            'code' => 1, 'message' => 'Success', 'data' => CaseResource::make($data)
        ]);
    }

    public function destroy($id)
    {
        CaseModel::find(request()->id)->update(['status_id' => -1]);
        return response()->json([
            'code' => 1, 'message' => 'Success', 'data' => CaseResource::collection($this->service->index())
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->all();

        if (is_null($data) || is_null($data['case'])) {
            return response()->json(['code' => 0, 'message' => 'დაფიქსირდა შეცდომა'], 400);
        }

        $this->service->store($data);

        return response()->json(['code' => 1, 'message' => 'ოპერაცია წარმატებით დასრულდა',]);
    }

    public function update(Request $request)
    {
    }
}
