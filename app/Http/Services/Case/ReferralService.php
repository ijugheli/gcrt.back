<?php

namespace App\Http\Services\Case;

use App\Http\Services\Case\BaseCaseInterface;
use App\Models\Case\CaseReferral;

class ReferralService extends BaseCaseService implements BaseCaseInterface
{
    public function index($caseID = null)
    {
        return CaseReferral::where('case_id', $caseID)->where('status_id', 1)->get();
    }

    public function show($id)
    {
        // TODO: Implement show() method.
    }

    public function store($data, $caseID = null)
    {
        return $this->handleModels($data, $caseID, CaseReferral::class);
    }

    public function update($data)
    {
        return $this->handleModel($data, CaseReferral::class);
    }

    public function destroy($id): void
    {
        CaseReferral::find($id)->update(['status_id' => -1]);
    }
}
