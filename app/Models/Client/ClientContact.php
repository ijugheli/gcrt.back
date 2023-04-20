<?php

namespace App\Models\Client;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class ClientContact extends Model
{
    protected $table = "client_contact_info_records";
    protected $fillable = [
        'id',
        'client_id',
        'phone_number',
        'home_phone_number',
        'personal_email',
        'work_phone_number',
        'work_internal_phone_number',
        'work_email',
        'fax',
    ];
}
