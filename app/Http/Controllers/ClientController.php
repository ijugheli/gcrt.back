<?php

namespace App\Http\Controllers;

use App\Http\Resources\ClientResource;
use App\Http\Services\ClientService;
use Illuminate\Http\Request;
use App\Models\Client\Client;
use App\Models\Client\ClientContact;
use App\Models\Client\ClientAddress;
use App\Models\Client\ClientAdditional;


class ClientController extends Controller
{
    public function save(Request $request, ClientService $service)
    {
        $data = $request->all();

        if (isset($data['main']['id']) && $data['main']['id'] != null) {
            $service->update($data, $data['main']['id']);
        } else {
            $service->create($data);
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
