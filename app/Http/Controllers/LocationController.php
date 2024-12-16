<?php

namespace App\Http\Controllers;

use App\Http\Requests\Location\LocationStoreRequest;
use App\Http\Requests\Location\LocationUpdateRequest;
use App\Models\Api\Counter;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LocationController extends Controller
{
    public function index(Request $request)
    {
        $page = $request->input('per_page', 10);
        $current_page = $request->input('current_page', 1);
        $search = $request->input('search');

        $query = Location::orderBy('LocationName', 'asc');

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('LocationName', 'like', "%$search%");
            });
        }

        $totalItems = $query->count();
        $locations = $query
            ->skip(($current_page - 1) * $page)
            ->take($page)
            ->get();

        $totalPages = ceil($totalItems / $page);
        $response = [
            'currentPage' => $current_page,
            'rowsPerPage' => $page,
            'totalPages' => $totalPages,
            'data' => $locations,
        ];

        return response()->json($response);
    }

    public function store(LocationStoreRequest $request)
    {
        $data = $request->validated();

        $data['RowId'] = Location::getNextRowId();
        $data['CreatedDate'] = date('Y-m-d H:i:s');

        DB::beginTransaction();
        try {
            $ctrFormat = str_replace([' ', '-'], '', $data['name']);
            $ctrFormat = (strlen($ctrFormat) > 2) ? strtoupper(substr($ctrFormat, 0, 2)) : $data['name'];

            $counter = Counter::where('CounterId', 'LC')->first();
            if ($counter) {
                $counter->CounterNumber++;
                $counter->save();

                $count = $counter->CounterNumber;
            } else {
                $count = 1;
            }

            $code = $ctrFormat . $count;

            Location::create([
                'LocationCode' => $code,
                'LocationName' => $data['name'],
                'IsLocation' => $data['is_location'] ? true : false,
                'IsProjectLocation' => $data['is_project_location'] ? true : false,
                'IsDefault' => isset($data['is_default']) ? true : false,
                'CreatedDate' => $data['CreatedDate'],
                // 'CreatedBy' => Auth::user()->name
                'CreatedBy' => "1",
                "LastUpdatedBy" => "1"
            ]);

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Location created successfully!'
            ], 201);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function update(LocationUpdateRequest $request, $id)
    {
        Log::debug($request->all());
        $data = $request->validated();
        $data['LastUpdatedDate'] = date('Y-m-d H:i:s');

        try {
            $location = Location::findOrFail($id);

            $location->LocationName = $data['name'];
            $location->IsLocation = $data['is_location'] ? true : false;
            $location->IsProjectLocation = $data['is_project_location'] ? true : false;
            $location->IsDefault =  isset($data['is_default']) ? true : false;
            $location->Status = $data['status'];
            $location->Active = $data['status'] === 'A' ? 1 : 0;
            $location->LastUpdatedDate = $data['LastUpdatedDate'];

            $location->save();

            return response()->json([
                'success' => true,
                'message' => 'Location updated successfully!'
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
        $location = Location::findOrFail($id);
        $location->delete();

        return response()->json([
            'success' => true,
            'message' => 'Location deleted successfully!'
        ], 200);
    }
}
