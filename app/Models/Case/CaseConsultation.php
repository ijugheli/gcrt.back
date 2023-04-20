<?php

namespace App\Models\Case;

use DateTime;
use Illuminate\Database\Eloquent\Model;

class CaseConsultation extends Model
{
    protected $table = "consultation_records";
    protected $fillable = [
        'id',
        'case_id',
        'status_id',
        'consultant',
        'date',
        'type',
        'duration',
        'consultant_record',
        'consultant_prescription',
    ];


    public function setDateAttribute($value)
    {
        $this->attributes['date'] = (new DateTime($value))->format('Y-m-d h:m:s');;
    }
}
