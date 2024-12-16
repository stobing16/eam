<?php

namespace App\Http\Controllers\Api\Android;

use App\Http\Controllers\Controller;
use App\Models\Api\TxOpnameAssetAndroidSingleTemp;
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

    public function getOpnameOrderAndroidDetailList(Request $request)
    {
        $barcode = $request->input('barcode');
        // Log::debug(json_encode([$barcode, $id]));

        $res = DB::select("EXEC RetrieveBarcodeCheck @Barcode = :code", ['code' => $barcode]);
        return response()->json($res);
    }

    public function saveAsset(Request $request)
    {
        $request->validate([
            'opname' => 'required',
            'barcode' => 'required',
            'condition' => 'required',
        ]);

        try {

            $barcode = trim($request->barcode);
            $opname = TxOpnameAssetAndroidSingleTemp::where('Barcode', $barcode)->where('OpnameOrderId', $request->opname)->doesntExist();
            Log::debug($opname);
            if ($opname) {
                TxOpnameAssetAndroidSingleTemp::create([
                    'OpnameOrderId' => $request->opname,
                    'Barcode' => $barcode,
                    'Condition' => $request->condition,
                    'CreatedDate' => date('Y-m-d H:i:s'),
                    'CreatedBy' => 'yocky'
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Asset Opname created successfully!'
            ], 201);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }
}
