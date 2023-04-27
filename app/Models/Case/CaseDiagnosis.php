<?php

namespace App\Models\Case;

use DateTime;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class CaseDiagnosis extends Model
{
    protected $table = "diagnosis_records";
    protected $fillable = [
        'id',
        'case_id',
        'status_id',
        'status',
        'type',
        'icd',
        'links_with_trauma',
        'diagnosis_icd10',
        'diagnosis_dsmiv',
        'diagnosis_date',
        'comment',
    ];

    public function getDiagnosisDateAttribute($value)
    {
        return  is_null($value) ? null : Carbon::parse($value)->format('d/m/y');
    }

    public function setDiagnosisDateAttribute($value)
    {
        $this->attributes['diagnosis_date'] = is_null($value) ? null : Carbon::parse($value);;
    }
}
