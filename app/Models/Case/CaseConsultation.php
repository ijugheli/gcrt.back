<?php

namespace App\Models\Case;

use DateTime;
use Carbon\Carbon;
use App\Http\Helpers\Helper;
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
        'client_status',
    ];

    public function getDateAttribute($value)
    {
        return is_null($value) ? null : Carbon::parse($value)->format('d/m/Y');
    }

    public function setDateAttribute($value)
    {
        $this->attributes['date'] = is_null($value) ? null : Helper::formatDate($value);
    }
}
