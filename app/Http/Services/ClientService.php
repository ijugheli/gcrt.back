<?php

namespace App\Http\Services;

use App\Models\Client\Client;
use App\Models\Client\ClientAddress;
use App\Models\Client\ClientContact;
use App\Models\Client\ClientAdditional;

class ClientService
{

    public function index()
    {
        return Client::where('status_id', 1)->with(['additionalInfo', 'contact', 'address'])->get();
    }

    public function show($id)
    {
        return Client::where('status_id', 1)->with(['additionalInfo', 'contact', 'address'])->find($id);
    }

    public function destroy($id)
    {
        Client::find($id)->update(['status_id' => -1]);
    }

    public function update($data, int $id): void
    {
        Client::find($id)->update($data['main']);
        ClientAdditional::find($id)->update($data['additional']);
        ClientContact::find($id)->update($data['contact']);
        ClientAddress::find($id)->update($data['address']);
    }

    public function create($data)
    {
        $client = Client::create($data['main']);
        ClientAdditional::create(['client_id' => $client->id, ...$data['additional']]);
        ClientContact::create(['client_id' => $client->id, ...$data['contact']]);
        ClientAddress::create(['client_id' => $client->id, ...$data['address']]);

        $client->client_code = $client->client_code . '[' . $client->id . ']';
        $client->save();

        return $client->id;
    }
}
