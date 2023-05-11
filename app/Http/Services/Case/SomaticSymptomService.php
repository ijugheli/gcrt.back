<?php

namespace App\Http\Services\Case;

use App\Http\Services\Case\BaseCaseInterface;
use App\Models\Case\CaseSomaticSymptom;

class SomaticSymptomService extends BaseCaseService implements BaseCaseInterface
{
    public function index($caseID = null)
    {
        return CaseSomaticSymptom::where('case_id', $caseID)->where('status_id', 1)->get();
    }

    public function show($id)
    {
        // TODO: Implement show() method.
    }

    public function store($data, $caseID = null)
    {
        return $this->handleModels($data, $caseID, CaseSomaticSymptom::class);
    }

    public function update($data)
    {
        return $this->handleModel($data, CaseSomaticSymptom::class);
    }

    public function destroy($id)
    {
        CaseSomaticSymptom::find($id)->update(['status_id' => -1]);
    }
}
