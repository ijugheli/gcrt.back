<?php

namespace App\Models\Case;

use Carbon\Carbon;
use App\Http\Helpers\Helper;
use Illuminate\Database\Eloquent\Model;

class CaseOtherSymptom extends Model
{
    protected $table = "other_symptom_records";
    protected $fillable = [
        'id',
        'case_id',
        'status_id',
        'registration_date',
        'somatic_symptom_comment',
        'mental_symptom_comment',
    ];

    public function getRegistrationDateAttribute($value)
    {
        return  is_null($value) ? null : Carbon::parse($value)->format('d/m/Y');
    }

    public function setRegistrationDateAttribute($value)
    {
        $this->attributes['registration_date'] = is_null($value) ? null : Helper::formatDate($value);
    }
}
