<?php

namespace App\Http\Services\Case;

use App\Http\Services\Case\BaseCaseInterface;
use App\Models\Case\CaseOtherSymptom;

class OtherSymptomService extends BaseCaseService implements BaseCaseInterface
{
    public function index($caseID = null)
    {
        return CaseOtherSymptom::where('case_id', $caseID)->where('status_id', 1)->get();
    }

    public function show($id)
    {
        // TODO: Implement show() method.
    }

    public function store($data, $caseID = null)
    {
        return $this->handleModels($data, $caseID, CaseOtherSymptom::class);
    }

    public function update($data)
    {
        return $this->handleModel($data, CaseOtherSymptom::class);
    }

    public function destroy($id)
    {
        CaseOtherSymptom::find($id)->update(['status_id' => -1]);
    }
}
