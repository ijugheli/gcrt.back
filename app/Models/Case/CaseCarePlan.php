<?php

namespace App\Models\Case;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class CaseCarePlan extends Model
{
    protected $table = "care_plan_records";
    protected $fillable = [
        'id',
        'status_id',
        'case_id',
        'category',
        'comment',
    ];
}
