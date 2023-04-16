<?php

namespace App\Http\Services;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Client\Client;
use App\Models\UserValidationCode;
use App\Models\Client\ClientAddress;
use App\Models\Client\ClientContact;
use Illuminate\Support\Facades\Mail;
use App\Models\Client\ClientAdditional;

class ClientService
{
    public function update($data, int $id): void
    {
        Client::whereKey($id)->update($data['main']);
        ClientAdditional::whereKey($id)->update($data['additional']);
        ClientContact::whereKey($id)->update($data['contact']);
        ClientAddress::whereKey($id)->update($data['address']);
    }

    public function create($data): void
    {
        $client = Client::create($data['main']);
        ClientAdditional::create(['client_id' => $client->id, ...$data['additional']]);
        ClientContact::create(['client_id' => $client->id, ...$data['contact']]);
        ClientAddress::create(['client_id' => $client->id, ...$data['address']]);

        $client->client_code = $client->client_code . '[' . $client->id . ']';
        $client->save();
    }
}
