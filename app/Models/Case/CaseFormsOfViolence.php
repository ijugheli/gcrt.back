<?php

namespace App\Models\Case;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class CaseFormsOfViolence extends Model
{
    protected $table = "forms_of_violence_records";
    protected $fillable = [
        'id',
        'status_id',
        'case_id',
        'category',
        'comment',
    ];
}
