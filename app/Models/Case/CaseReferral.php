<?php

namespace App\Models\Case;

use DateTime;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class CaseReferral extends Model
{
    protected $table = "referral_records";
    protected $fillable = [
        'id',
        'case_id',
        'status_id',
        'service_date',
        'type',
        'provider',
        'service_type',
        'price',
        'result',
    ];


    public function getServiceDateAttribute($value)
    {
        return  is_null($value) ? null : Carbon::parse($value)->format('d/m/y');
    }

    public function setServiceDateAttribute($value)
    {
        $this->attributes['service_date'] = is_null($value) ? null : Carbon::parse($value);;
    }
}
