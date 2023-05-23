<?php

namespace App\Http\Controllers\Case;

use Illuminate\Http\Request;
use App\Models\Case\CaseSomaticSymptom;
use App\Http\Controllers\Controller;
use App\Http\Services\Case\SomaticSymptomService;
use App\Http\Resources\BaseRelationshipResource;
use App\Http\Controllers\Case\CaseControllerInterface;

class SomaticSymptomController extends Controller implements CaseControllerInterface
{
    private $service;

    public function __construct(SomaticSymptomService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        //
    }

    public function show($id)
    {
        //
    }

    public function store(Request $request)
    {
        //
    }

    public function update(Request $request)
    {
        $data = $request->all();
        $caseID = intval($request->case_id);

        if (is_null($data)) {
            return response()->json(['code' => 0, 'message' => 'დაფიქსირდა შეცდომა'], 400);
        }

        $this->service->store($data, $caseID);

        return response()->json(['code' => 1, 'message' => 'Success', 'data' => BaseRelationshipResource::collection($this->service->index($caseID))]);
    }

    public function destroy($case_id)
    {
        $caseID = request()->case_id;
        $data = request()->all();
        $ids = $data['data'];
        $this->service->destroy($ids);
        return response()->json([
            'code' => 1, 'message' => 'Success', 'data' => BaseRelationshipResource::collection($this->service->index($caseID))
        ]);
    }
}
