<?php

namespace App\Http\Controllers\Api\Report;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;

class AssetLogHistoryController extends Controller
{
    public function index(Request $request)
    {
        $assetCode = $request->input('asset_code');
        if (!$assetCode) {
            return response()->json([
                'error' => 'The asset_code parameter is required.'
            ], 400);
        }

        $data = DB::select('EXEC AssetHistoryLogSub @sAssetCode = ?', [$assetCode]);

        return response()->json($data);
    }
}
