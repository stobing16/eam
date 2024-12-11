<?php

namespace App\Http\Controllers\Api\Master;

use App\Http\Controllers\Controller;
use App\Http\Requests\Brand\BrandStoreRequest;
use App\Http\Requests\Brand\BrandUpdateRequest;
use App\Models\Api\Brand;
use App\Models\Api\Counter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BrandController extends Controller
{
    public function index(Request $request, $code)
    {
        $brand = Brand::where('AssetTypeCode', $code)->orderBy('BrandName', 'asc')->get();
        $response = [
            'data' => $brand,
        ];

        return response()->json($response);
    }

    public function store(BrandStoreRequest $request)
    {
        $data = $request->validated();

        DB::beginTransaction();
        try {
            $ctrFormat = str_replace([' ', '-'], '', $data['name']);
            $ctrFormat = (strlen($ctrFormat) > 2) ? strtoupper(substr($ctrFormat, 0, 2)) : $data['name'];

            $counter = Counter::where('CounterId', 'BR')->first();
            if ($counter) {
                $counter->CounterNumber++;
                $counter->save();

                $count = $counter->CounterNumber;
            } else {
                $count = 1;
            }


            $code = $ctrFormat . $count;

            Brand::create([
                'MainGroupCode' => $data['groupCode'],
                'AssetTypeCode' => $data['assetCode'],
                'BrandCode' => $code,
                'BrandName' => $data['name'],
                'CreatedDate' => date('Y-m-d H:i:s'),
                'CreatedBy' => Auth::user()->id,
                'LastUpdatedBy' => "1"
            ]);

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Brand created successfully!'
            ], 201);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function update(BrandUpdateRequest $request, $id)
    {
        $data = $request->validated();

        try {
            $brandForm = Brand::findOrFail($id);

            $brandForm->BrandName = $data['name'];
            $brandForm->Status = $data['status'];
            $brandForm->Active = $data['status'] === 'A' ? 1 : 0;
            $brandForm->LastUpdatedDate = date('Y-m-d H:i:s');
            $brandForm->LastUpdatedBy = 1;

            $brandForm->save();

            return response()->json([
                'success' => true,
                'message' => 'Brand updated successfully!'
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
        $mainGroup = Brand::findOrFail($id);
        $mainGroup->delete();

        return response()->json([
            'success' => true,
            'message' => 'Asset Type deleted successfully!'
        ], 200);
    }
}
