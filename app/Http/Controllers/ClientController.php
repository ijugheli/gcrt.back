<?php

namespace App\Http\Controllers;

use App\Http\Resources\ClientResource;
use App\Http\Services\ClientService;
use Illuminate\Http\Request;
use App\Models\Client\Client;


class ClientController extends Controller
{
    private $service;

    public function __construct(ClientService $service)
    {
        $this->service = $service;
    }

    public function store(Request $request)
    {
        $data = $request->all();
        $clientID = null;

        if (isset($data['main']['id']) && $data['main']['id'] != null) {
            $clientID = $data['main']['id'];
            $this->service->update($data, $data['main']['id']);
        } else {
            $clientID = $this->service->create($data);
        }

        return response()->json(['code' => 1, 'message' => 'ოპერაცია წარმატებით დასრულდა', 'data' => ClientResource::make($this->service->show($clientID))]);
    }

    public function index()
    {
        return response()->json([
            'code' => 1, 'message' => 'Success', 'data' => ClientResource::collection($this->service->index())
        ]);
    }

    public function show($id)
    {
        $data = $this->service->show($id);

        if (is_null($data)) {
            return response()->json([
                'code' => 0, 'message' => 'ჩანაწერი ვერ მოიძებნა',
            ], 400);
        }

        return response()->json([
            'code' => 1, 'message' => 'Success', 'data' => ClientResource::make($data)
        ]);
    }

    public function destroy($id)
    {
        $this->service->destroy($id);
        return response()->json([
            'code' => 1, 'message' => 'Success', 'data' => ClientResource::collection($this->service->index())
        ]);
    }
}
