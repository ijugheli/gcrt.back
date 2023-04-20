<?php

namespace App\Models\Case;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class ClientAdditional extends Model
{
    protected $table = "client_additional_info_records";
    protected $fillable = [
        'id',
        'client_id',
        'nationality',
        'education',
        'marital_status',
        'family_members',
        'has_social_support',
        'has_insurance',
        'work_address',
        'profession',
    ];
}
