<?php

namespace App\Models\Client;

use App\Http\Helpers\Helper;
use DateTime;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Client extends Model
{
    protected $table = "client_main_section_records";

    protected $fillable = [
        'id',
        'client_code',
        'branch',
        'registration_date',
        'name',
        'surname',
        'personal_id',
        'birth_date',
        'age',
        'age_group',
        'category_group_id',
        'repeating_client',
        'gender',
        'status_id',
    ];

    public function additionalInfo()
    {
        return $this->hasOne(ClientAdditional::class, 'client_id', 'id');
    }

    public function contact()
    {
        return $this->hasOne(ClientContact::class, 'client_id', 'id');
    }

    public function address()
    {
        return $this->hasOne(ClientAddress::class, 'client_id', 'id');
    }

    public function getRegistrationDateAttribute($value)
    {
        return is_null($value) ? null : Carbon::parse($value)->format('d/m/y');
    }

    public function getBirthDateAttribute($value)
    {
        return is_null($value) ? null : Carbon::parse($value)->format('d/m/y');
    }

    public function setRegistrationDateAttribute($value)
    {

        $this->attributes['registration_date'] = is_null($value) ? null : Helper::formatDate($value);
    }

    public function setBirthDateAttribute($value)
    {
        $this->attributes['birth_date'] = is_null($value) ? null : Helper::formatDate($value);
    }
}
