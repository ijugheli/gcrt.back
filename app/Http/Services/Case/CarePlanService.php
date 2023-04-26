<?php

namespace App\Http\Services\Case;

use App\Models\Case\CaseCarePlan;
use App\Http\Services\Case\BaseCaseInterface;

class CarePlanService extends BaseCaseService implements BaseCaseInterface
{
    public function index($caseID = null)
    {
        return CaseCarePlan::where('case_id', $caseID)->where('status_id', 1)->get();
    }

    public function show($id)
    {
        // TODO: Implement show() method.
    }

    public function store($data, $caseID = null)
    {
        $this->handleModels($data, $caseID, CaseCarePlan::class);
        if ($caseID != null) {
            $builder = CaseCarePlan::where('case_id', $caseID);

            if (count($data) <= 0) {
                $builder->delete();
                return;
            }

            $ids = collect($data)->pluck('id')->toArray();
            $builder->whereNotIn('id', $ids)->delete();
        }
    }

    public function update($data)
    {
        return $this->handleModel($data, CaseCarePlan::class);
    }

    public function destroy($id)
    {
        // TODO: Implement destroy() method.
    }
}
