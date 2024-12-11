<?php

namespace App\Http\Controllers\Api\Android;

use App\Http\Controllers\Controller;
use App\Models\Api\TxOpnameOrder;
use Illuminate\Http\Request;

class AssetOpnameSingleController extends Controller
{
    public function getOpnameOrder(Request $request)
    {
        $data = TxOpnameOrder::select(['OpnameOrderId',])->get();
        $response = [
            'data' => $data,
        ];

        return response()->json($response);
    }

    public function saveAsset(Request $request) {}
}
