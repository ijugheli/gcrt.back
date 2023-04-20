<?php

namespace App\Models\Case;

use DateTime;
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

    public function setServiceDateAttribute($value)
    {
        $this->attributes['service_date'] = (new DateTime($value))->format('Y-m-d h:m:s');;
    }
}
