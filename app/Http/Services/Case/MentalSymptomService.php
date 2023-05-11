<?php

namespace App\Http\Services\Case;

use App\Http\Services\Case\BaseCaseInterface;
use App\Models\Case\CaseMentalSymptom;

class MentalSymptomService extends BaseCaseService implements BaseCaseInterface
{
    public function index($caseID = null)
    {
        return CaseMentalSymptom::where('case_id', $caseID)->where('status_id', 1)->get();
    }

    public function show($id)
    {
        // TODO: Implement show() method.
    }

    public function store($data, $caseID = null)
    {
        return $this->handleModels($data, $caseID, CaseMentalSymptom::class);
    }

    public function update($data)
    {
        return $this->handleModel($data, CaseMentalSymptom::class);
    }

    public function destroy($id)
    {
        CaseMentalSymptom::find($id)->update(['status_id' => -1]);
    }
}
