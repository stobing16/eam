<?php

namespace App\Http\Controllers\Api\Android;

use App\Http\Controllers\Controller;
use App\Models\Api\TxOpnameOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AssetOpnameSingleController extends Controller
{
    public function getOpnameOrderAndroidList(Request $request)
    {
        $res = DB::select("
            SELECT
                TxOpnameOrder.OpnameOrderId as ID,
                CONCAT(
                    TxOpnameOrder.OpnameOrderId ,
                    ' - ',
                    MsLocation.LocationName,
                    ' - ',
                    CASE
                        WHEN TxOpnameOrder.OpnameOrdertype = 2 THEN 'Asset'
                        ELSE '-'
                    END
                ) AS Data
            FROM TxOpnameOrder
            JOIN MsLocation ON MsLocation.LocationCode = TxOpnameOrder.LocationCode
        ");

        return response()->json($res);
    }

    public function saveAsset(Request $request) {}
}
