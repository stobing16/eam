<?php

namespace App\Http\Controllers;

use App\Http\Requests\SubLocation\SubLocationStoreRequest;
use App\Http\Requests\SubLocation\SubLocationUpdateRequest;
use App\Models\Api\Counter;
use App\Models\SubLocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SubLocationController extends Controller
{
    public function index(Request $request)
    {
        $page = $request->input('per_page', 10);
        $current_page = $request->input('current_page', 1);
        $search = $request->input('search');

        $query = SubLocation::orderBy('SubLocationName', 'asc');

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('SubLocationName', 'like', "%$search%");
            });
        }

        $totalItems = $query->count();
        $subLocations = $query
            ->skip(($current_page - 1) * $page)
            ->take($page)
            ->get();

        $totalPages = ceil($totalItems / $page);
        $response = [
            'currentPage' => $current_page,
            'rowsPerPage' => $page,
            'totalPages' => $totalPages,
            'data' => $subLocations,
        ];

        return response()->json($response);
    }

    public function store(SubLocationStoreRequest $request)
    {
        $data = $request->validated();

        $data['RowId'] = SubLocation::getNextRowId();
        $data['CreatedDate'] = date('Y-m-d H:i:s');

        DB::beginTransaction();
        try {
            $ctrFormat = str_replace([' ', '-'], '', $data['name']);
            $ctrFormat = (strlen($ctrFormat) > 2) ? strtoupper(substr($ctrFormat, 0, 2)) : $data['name'];

            $counter = Counter::where('CounterId', 'SLC')->first();
            if ($counter) {
                $counter->CounterNumber++;
                $counter->save();

                $count = $counter->CounterNumber;
            } else {
                $count = 1;
            }

            $code = $ctrFormat . $count;

            SubLocation::create([
                'SubLocationCode' => $code,
                'SubLocationName' => $data['name'],
                'CreatedDate' => $data['CreatedDate'],
                // 'CreatedBy' => Auth::user()->name,
                'CreatedBy' => "1",
                "LastUpdatedBy" => "1"
            ]);

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Sub Location created successfully!'
            ], 201);
        } catch (\Throwable $th) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function update(SubLocationUpdateRequest $request, $id)
    {
        $data = $request->validated();
        $data['LastUpdatedDate'] = date('Y-m-d H:i:s');

        try {
            $subLocation = SubLocation::findOrFail($id);

            $subLocation->SubLocationName = $data['name'];
            $subLocation->Status = $data['status'];
            $subLocation->Active = $data['status'] === 'A' ? 1 : 0;
            $subLocation->LastUpdatedDate = $data['LastUpdatedDate'];

            $subLocation->save();

            return response()->json([
                'success' => true,
                'message' => 'Sub Location updated successfully!'
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
        $subLocation = SubLocation::findOrFail($id);
        $subLocation->delete();

        return response()->json([
            'success' => true,
            'message' => 'Sub Location deleted successfully!'
        ], 200);
    }
}
