<?php

namespace App\Http\Controllers\Api\Master;

use App\Http\Controllers\Controller;
use App\Http\Requests\AssetHirarki\MainGroupStoreRequest;
use App\Http\Requests\AssetHirarki\MainGroupUpdateRequest;
use App\Models\Api\Counter;
use App\Models\Api\MainGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MainGroupController extends Controller
{
    public function index(Request $request)
    {
        $page = (int) $request->input('per_page', 10);
        $current_page = (int) $request->input('current_page', 1);
        $search = $request->input('search', '');

        $query = MainGroup::orderBy('MainGroupName', 'asc');


        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('MainGroupName', 'like', "%$search%")
                    ->orWhere('MainGroupCode', 'like', "%$search%");
            });
        }

        $totalItems = $query->count();

        $mainGroup = $query
            ->skip(($current_page - 1) * $page)
            ->take($page)
            ->get();

        $totalPages = ceil($totalItems / $page);

        $response = [
            'currentPage' => $current_page,
            'rowsPerPage' => $page,
            'totalPages' => $totalPages,
            'data' => $mainGroup,
        ];

        return response()->json($response);
    }

    public function store(MainGroupStoreRequest $request)
    {
        $data = $request->validated();

        DB::beginTransaction();
        try {
            $ctrFormat = str_replace([' ', '-'], '', $data['name']);
            $ctrFormat = (strlen($ctrFormat) > 2) ? strtoupper(substr($ctrFormat, 0, 2)) : $data['name'];

            $counter = Counter::where('CounterId', 'MG')->first();
            if ($counter) {
                $counter->CounterNumber++;
                $counter->save();

                $count = $counter->CounterNumber;
            } else {
                $count = 1;
            }

            $code = $ctrFormat . $count;

            MainGroup::create([
                'MainGroupName' => $data['name'],
                'MainGroupCode' => $code,
                'CreatedDate' => date('Y-m-d H:i:s'),
                'CreatedBy' => 'yocky',
                'LastUpdatedBy' => 1
            ]);

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Main Group created successfully!'
            ], 201);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }
    public function update(MainGroupUpdateRequest $request, $id)
    {
        $data = $request->validated();

        try {
            $mainGroup = MainGroup::findOrFail($id);

            $mainGroup->MainGroupName = $data['name'];
            $mainGroup->Status = $data['status'];
            $mainGroup->Active = $data['status'] === 'A' ? 1 : 0;
            $mainGroup->LastUpdatedDate = date('Y-m-d H:i:s');
            $mainGroup->LastUpdatedBy = 1;

            $mainGroup->save();

            return response()->json([
                'success' => true,
                'message' => 'Main Group updated successfully!'
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
        $mainGroup = MainGroup::findOrFail($id);
        $mainGroup->delete();

        return response()->json([
            'success' => true,
            'message' => 'MainGroup deleted successfully!'
        ], 200);
    }
}
