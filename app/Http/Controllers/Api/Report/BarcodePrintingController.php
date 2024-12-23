<?php

namespace App\Http\Controllers\Api\Report;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BarcodePrintingController extends Controller
{
    public function index(Request $request)
    {
        $assetCategory = $request->input('AssetCategoryName');
        $mainGroup = $request->input('MainGroup');
        $assetType = $request->input('AssetType');
        $brand = $request->input('Brand');
        $model = $request->input('Model');
        $condition = $request->input('Condition');
        $supplier = $request->input('Supplier');
        $search = $request->input('Search');

        if (!$assetCategory) {
            return response()->json([
                'error' => 'AssetCategoryName parameter is required.'
            ], 400);
        }

        $mainGroup = $mainGroup ?? '-1';
        $assetType = $assetType ?? '-1';
        $brand = $brand ?? '-1';
        $model = $model ?? '-1';
        $condition = $condition ?? '-1';
        $supplier = $supplier ?? '-1';
        $search = $search ?? '';

        $data = DB::select('EXEC GetAssetListBarcodePrinting ?, ?, ?, ?, ?, ?, ?, ?', [
            $assetCategory,
            $mainGroup,
            $assetType,
            $brand,
            $model,
            $condition,
            $supplier,
            $search,
        ]);

        return response()->json($data);
    }
}

