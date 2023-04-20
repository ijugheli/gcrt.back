<?php

namespace App\Http\Services\Case;

use App\Http\Services\Case\BaseCaseInterface;
use App\Models\Case\CaseCarePlan;

class CarePlanService extends BaseCaseService implements BaseCaseInterface
{
    public function index($caseID =null)
    {
        // TODO: Implement index() method.
    }

    public function show($id)
    {
        // TODO: Implement show() method.
    }

    public function store($data, $caseID = null)
    {
        return $this->handleModels($data, $caseID, CaseCarePlan::class);
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
