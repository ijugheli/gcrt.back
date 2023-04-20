<?php

namespace App\Http\Controllers\Case;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Services\Case\FormsOfViolenceService;
use App\Http\Controllers\Case\CaseControllerInterface;
use App\Models\Case\CaseFormsOfViolence;

class FormsOfViolenceController extends Controller implements CaseControllerInterface
{
    private $service;

    public function __construct(FormsOfViolenceService $service)
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
            return response()->json(['code' => 0, 'message' => 'დაფიქსირდა შეცდომა'],400);
        }

        if (!$this->service->update($data)) {
            return response()->json(['code' => 0, 'message' => 'ჩანაწერი ვერ მოიძებნა'],400);
        }

        return response()->json(['code' => 1, 'message' => 'Success', 'data' => CaseFormsOfViolence::where('case_id', $data['case_id'])->get()]);
    }

    public function destroy($id)
    {
        //
    }
}