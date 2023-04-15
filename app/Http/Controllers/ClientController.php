<?php

namespace App\Http\Controllers;

use App\Http\Resources\ClientResource;
use Illuminate\Http\Request;
use App\Models\Client\Client;
use App\Models\Client\ClientContact;
use App\Models\Client\ClientAddress;
use App\Models\Client\ClientAdditional;


class ClientController extends Controller
{
    public function save(Request $request)
    {
        $data = $request->all();

        $client = Client::updateOrCreate($data['main']);
        ClientAdditional::updateOrCreate(['client_id' => $client->id, ...$data['additional']]);
        ClientContact::updateOrCreate(['client_id' => $client->id, ...$data['contact']]);
        ClientAddress::updateOrCreate(['client_id' => $client->id, ...$data['address']]);

        if ($client->wasRecentlyCreated) {
            $client->client_code = $client->client_code . '[' . $client->id . ']';
            $client->save();
        }

        return response()->json(['code' => 1, 'message' => 'ოპერაცია წარმატებით დასრულდა',]);
    }

    public function list()
    {
        return response()->json([
            'code' => 1, 'message' => 'Success', 'data' => ClientResource::collection(Client::where('status_id', 1)->with(['additionalInfo', 'contact', 'address'])->get())
        ]);
    }

    public function destroy()
    {
        Client::whereKey(request()->client_id)->update(['status_id' => -1]);
        return response()->json([
            'code' => 1, 'message' => 'Success', 'data' => ClientResource::collection(Client::where('status_id', 1)->with(['additionalInfo', 'contact', 'address'])->get())
        ]);
    }
}
