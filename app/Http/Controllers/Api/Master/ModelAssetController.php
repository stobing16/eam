<?php

namespace App\Http\Controllers\Api\Master;

use App\Http\Controllers\Controller;
use App\Http\Requests\ModelAsset\ModelAssetStoreRequest;
use App\Http\Requests\ModelAsset\ModelAssetUpdateRequest;
use App\Models\Api\Counter;
use App\Models\Api\ModelAsset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ModelAssetController extends Controller
{
    public function index(Request $request, $code)
    {
        $models = ModelAsset::where('BrandCode', $code)->orderBy('ModelName', 'asc')->get();
        $response = [
            'data' => $models,
        ];

        return response()->json($response);
    }

    public function store(ModelAssetStoreRequest $request)
    {
        $data = $request->validated();

        DB::beginTransaction();
        try {
            $ctrFormat = str_replace([' ', '-'], '', $data['name']);
            $ctrFormat = (strlen($ctrFormat) > 2) ? strtoupper(substr($ctrFormat, 0, 2)) : $data['name'];

            $counter = Counter::where('CounterId', 'MD')->first();
            if ($counter) {
                $counter->CounterNumber++;
                $counter->save();

                $count = $counter->CounterNumber;
            } else {
                $count = 1;
            }


            $code = $ctrFormat . $count;

            ModelAsset::create([
                'MainGroupCode' => $data['groupCode'],
                'AssetTypeCode' => $data['assetCode'],
                'BrandCode' => $data['brandCode'],
                'ModelCode' => $code,
                'ModelName' => $data['name'],
                'CreatedDate' => date('Y-m-d H:i:s'),
                'CreatedBy' => Auth::user()->id,
                'LastUpdatedBy' => "1"
            ]);

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Model created successfully!'
            ], 201);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function update(ModelAssetUpdateRequest $request, $id)
    {
        $data = $request->validated();

        try {
            $modelForm = ModelAsset::findOrFail($id);

            $modelForm->ModelName = $data['name'];
            $modelForm->Status = $data['status'];
            $modelForm->Active = $data['status'] === 'A' ? 1 : 0;
            $modelForm->LastUpdatedDate = date('Y-m-d H:i:s');
            $modelForm->LastUpdatedBy = 1;

            $modelForm->save();

            return response()->json([
                'success' => true,
                'message' => 'Model updated successfully!'
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
        $mainGroup = ModelAsset::findOrFail($id);
        $mainGroup->delete();

        return response()->json([
            'success' => true,
            'message' => 'Asset Type deleted successfully!'
        ], 200);
    }
}
