<?php

namespace App\Models\Client;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class ClientAddress extends Model
{
    protected $table = "client_address_info_records";
    protected $fillable = [
        'id',
        'client_id',
        'location_id',
        'address',
        'zip_code',
        'previous_address'
    ];
}
