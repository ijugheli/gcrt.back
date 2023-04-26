<?php

namespace App\Http\Services\Case;

use App\Http\Services\Case\BaseCaseInterface;
use App\Models\Case\CaseFormsOfViolence;

class FormsOfViolenceService extends BaseCaseService implements BaseCaseInterface
{
    public function index($caseID = null)
    {
        return CaseFormsOfViolence::where('case_id', $caseID)->where('status_id', 1)->get();
    }

    public function show($id)
    {
        // TODO: Implement show() method.
    }

    public function store($data, $caseID = null)
    {
        $this->handleModels($data, $caseID, CaseFormsOfViolence::class);
        if ($caseID != null) {
            $builder = CaseFormsOfViolence::where('case_id', $caseID);

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
        return $this->handleModel($data, CaseFormsOfViolence::class);
    }

    public function destroy($id)
    {
        // TODO: Implement destroy() method.
    }
}
