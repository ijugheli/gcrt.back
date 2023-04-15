<?php

namespace App\Models\Client;

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
    public function dataType()
    {
        return $this->hasOne(InputDataType::class, 'id', 'input_data_type');
    }

    public function viewType()
    {
        return $this->hasOne(InputViewType::class, 'id', 'input_view_type');
    }

    public function source()
    {
        return $this->hasOne(Attr::class, 'id', 'source_attr_id');
    }

    public function isSection()
    {
        return $this->type == 2;
    }

    // public function scopeActive(Builder $query): void
    // {
    //     $query->where('status_id', 1);
    // }
}
