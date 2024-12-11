<?php

namespace App\Http\Controllers\Api\Master;

use App\Http\Controllers\Controller;
use App\Http\Requests\AssetType\AssetTypeStoreRequest;
use App\Http\Requests\AssetType\AssetTypeUpdateRequest;
use App\Models\Api\AssetType;
use App\Models\Api\Counter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AssetTypeController extends Controller
{
    public function index(Request $request, $code)
    {
        $assetType = AssetType::where('MainGroupCode', $code)->orderBy('AssetType', 'asc')->get();
        $response = [
            'data' => $assetType,
        ];

        return response()->json($response);
    }

    public function store(AssetTypeStoreRequest $request)
    {
        $data = $request->validated();

        DB::beginTransaction();
        try {
            $ctrFormat = str_replace([' ', '-'], '', $data['name']);
            $ctrFormat = (strlen($ctrFormat) > 2) ? strtoupper(substr($ctrFormat, 0, 2)) : $data['name'];

            $counter = Counter::where('CounterId', 'AT')->first();
            if ($counter) {
                $counter->CounterNumber++;
                $counter->save();

                $count = $counter->CounterNumber;
            } else {
                $count = 1;
            }

            $code = $ctrFormat . $count;

            AssetType::create([
                'MainGroupCode' => $data['code'],
                'AssetTypeCode' => $code,
                'AssetType' => $data['name'],
                'Alias' => $data['alias'],
                'CreatedDate' => date('Y-m-d H:i:s'),
                'CreatedBy' => Auth::user()->id,
                'LastUpdatedBy' => "1"
            ]);

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Asset Type created successfully!'
            ], 201);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function update(AssetTypeUpdateRequest $request, $id)
    {
        $data = $request->validated();

        try {
            $assetType = AssetType::findOrFail($id);

            $assetType->AssetType = $data['name'];
            $assetType->Alias = $data['alias'];
            $assetType->Status = $data['status'];
            $assetType->Active = $data['status'] === 'A' ? 1 : 0;
            $assetType->LastUpdatedDate = date('Y-m-d H:i:s');
            $assetType->LastUpdatedBy = 1;

            $assetType->save();

            return response()->json([
                'success' => true,
                'message' => 'Asset Type updated successfully!'
            ], 201);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function delete($id)
    {
        $mainGroup = AssetType::findOrFail($id);
        $mainGroup->delete();

        return response()->json([
            'success' => true,
            'message' => 'Asset Type deleted successfully!'
        ], 200);
    }
}
