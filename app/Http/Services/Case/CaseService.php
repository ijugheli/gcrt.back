<?php

namespace App\Http\Services\Case;

use App\Models\Case\CaseModel;
use App\Http\Services\Case\BaseCaseService;
use App\Http\Services\Case\CarePlanService;
use App\Http\Services\Case\ReferralService;
use App\Http\Services\Case\DiagnosisService;
use App\Http\Services\Case\BaseCaseInterface;
use App\Http\Services\Case\ConsultationService;
use App\Http\Services\Case\FormsOfViolenceService;

class CaseService extends BaseCaseService implements BaseCaseInterface
{
    private $consultation;
    private $diagnosis;
    private $carePlan;
    private $formsOfViolence;
    private $referral;

    public function __construct(DiagnosisService $diagnosis, CarePlanService $carePlan, FormsOfViolenceService $formsOfViolence, ConsultationService $consultation, ReferralService $referral)
    {
        $this->consultation = $consultation;
        $this->diagnosis = $diagnosis;
        $this->carePlan = $carePlan;
        $this->formsOfViolence = $formsOfViolence;
        $this->referral = $referral;
    }

    public function index($caseID = null)
    {
        return CaseModel::where('status_id', 1)->get();
    }

    public function show($id)
    {
        return CaseModel::find($id);
    }

    public function store($data, $caseID = null)
    {
        $caseID = null;

        if (!$this->hasID($data['case'])) {
            $case = CaseModel::create($data['case']);
            $caseID = $case->id;
        } else {
            $caseID = $data['case']['id'];
            CaseModel::find($data['case']['id'])->update($data['case']);
        }

        $this->diagnosis->store($data['diagnoses'], $caseID);
        $this->consultation->store($data['consultations'], $caseID);
        $this->referral->store($data['referrals'], $caseID);
        $this->formsOfViolence->store($data['forms_of_violences'], $caseID);
        $this->carePlan->store($data['care_plans'], $caseID);

        return $this->show($caseID);
    }

    public function destroy($id): void
    {
        CaseModel::find($id)->update(['status_id' => -1]);
    }

    public function update($data)
    {
    }
}
