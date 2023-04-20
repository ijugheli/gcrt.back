<?php

namespace App\Http\Controllers\Case;

use Illuminate\Http\Request;
use App\Models\Case\CaseReferral;
use App\Http\Controllers\Controller;
use App\Http\Services\Case\ReferralService;
use App\Http\Resources\BaseRelationshipResource;
use App\Http\Controllers\Case\CaseControllerInterface;

class ReferralController extends Controller implements CaseControllerInterface
{
    private $service;

    public function __construct(ReferralService $service)
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

        if (is_null($data)) {
            return response()->json(['code' => 0, 'message' => 'დაფიქსირდა შეცდომა'], 400);
        }

        if (!$this->service->update($data)) {
            return response()->json(['code' => 0, 'message' => 'ჩანაწერი ვერ მოიძებნა'], 400);
        }

        return response()->json(['code' => 1, 'message' => 'Success', 'data' => BaseRelationshipResource::collection($this->service->index($data['case_id']))]);
    }

    public function destroy($id)
    {
        $model = CaseReferral::find($id);
        $model->update(['status_id' => -1]);
        // return ::where('case_id', $model->case_id)->where('status_id', 1)->get();
        return response()->json([
            'code' => 1, 'message' => 'Success', 'data' => BaseRelationshipResource::collection($this->service->index($model->case_id))
        ]);
    }
}
