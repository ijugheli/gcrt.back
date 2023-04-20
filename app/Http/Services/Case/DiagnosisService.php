<?php

namespace App\Http\Services\Case;

use App\Http\Services\Case\BaseCaseInterface;
use App\Models\Case\CaseDiagnosis;

class DiagnosisService extends BaseCaseService implements BaseCaseInterface
{
    public function index($caseID = null)
    {
        return CaseDiagnosis::where('case_id', $caseID)->where('status_id', 1)->get();
    }

    public function show($id)
    {
        // TODO: Implement show() method.
    }

    public function store($data, $caseID = null)
    {
        return $this->handleModels($data, $caseID, CaseDiagnosis::class);
    }

    public function update($data)
    {
        return $this->handleModel($data, CaseDiagnosis::class);
    }

    public function destroy($id): void
    {
        CaseDiagnosis::find($id)->update(['status_id' => -1]);
    }
}
