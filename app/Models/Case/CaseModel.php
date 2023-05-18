<?php

namespace App\Models\Case;

use DateTime;
use Carbon\Carbon;
use App\Http\Helpers\Helper;
use Illuminate\Database\Eloquent\Model;

class CaseModel extends Model
{
    protected $table = "case_main_section_records";

    protected $fillable = [
        'id',
        'project_id',
        'case_manager_id',
        'client_id',
        'branch',
        'registration_date',
        'referral_body',
        'recommender',
        'incident',
        'incident_text',
        'social_status',
        'legal_status',
        'health_condition',
        'status_id',
    ];

    protected $with = [
        'referrals',
        'consultations',
        'diagnoses',
        'carePlans',
        'formsOfViolences'
    ];

    public function carePlans()
    {
        return $this->hasMany(CaseCarePlan::class, 'case_id', 'id')->where('status_id', 1);
    }

    public function formsOfViolences()
    {
        return $this->hasMany(CaseFormsOfViolence::class, 'case_id', 'id')->where('status_id', 1);
    }

    public function diagnoses()
    {
        return $this->hasMany(CaseDiagnosis::class, 'case_id', 'id')->where('status_id', 1);
    }

    public function consultations()
    {
        return $this->hasMany(CaseConsultation::class, 'case_id', 'id')->where('status_id', 1);
    }

    public function referrals()
    {
        return $this->hasMany(CaseReferral::class, 'case_id', 'id')->where('status_id', 1);
    }

    public function getRegistrationDateAttribute($value)
    {
        return  is_null($value) ? null : Carbon::parse($value)->format('d/m/Y');
    }

    public function setRegistrationDateAttribute($value)
    {
        $this->attributes['registration_date'] = is_null($value) ? null : Helper::formatDate($value);
    }
}
