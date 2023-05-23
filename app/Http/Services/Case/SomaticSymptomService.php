<?php

namespace App\Http\Services\Case;

use Carbon\Carbon;
use App\Models\Case\CaseSomaticSymptom;
use App\Http\Services\Case\BaseCaseInterface;

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
        $this->handleModels($data, $caseID, CaseSomaticSymptom::class);
        if ($caseID != null) {
            $builder = CaseSomaticSymptom::where('case_id', $caseID)->whereDate('record_date', Carbon::createFromFormat('d/m/Y', $data[0]['record_date'])->format('Y-m-d'));

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
        return $this->handleModel($data, CaseSomaticSymptom::class);
    }

    public function destroy($ids)
    {
        CaseSomaticSymptom::whereIn('id', $ids)->update(['status_id' => -1]);
    }
}
