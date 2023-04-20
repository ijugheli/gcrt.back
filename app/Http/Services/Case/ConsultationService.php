<?php

namespace App\Http\Services\Case;

use App\Http\Services\Case\BaseCaseInterface;
use App\Models\Case\CaseConsultation;

class ConsultationService extends BaseCaseService implements BaseCaseInterface
{
    public function index($caseID = null)
    {
        return CaseConsultation::where('case_id', $caseID)->where('status_id', 1)->get();
    }

    public function show($id)
    {
        // TODO: Implement show() method.
    }

    public function store($data, $caseID = null)
    {
        return $this->handleModels($data, $caseID, CaseConsultation::class);
    }

    public function update($data)
    {
        return $this->handleModel($data, CaseConsultation::class);
    }

    public function destroy($id)
    {
        CaseConsultation::find($id)->update(['status_id' => -1]);
    }
}
