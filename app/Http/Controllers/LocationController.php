<?php

namespace App\Http\Controllers;

use App\Http\Requests\Location\LocationStoreRequest;
use App\Http\Requests\Location\LocationUpdateRequest;
use App\Models\Location;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function index(Request $request)
    {
        $page = $request->input('per_page', 10);
        $current_page = $request->input('current_page', 1);
        $search = $request->input('search');

        $locations = Location::skip(($current_page - 1) * $page)->take($page)->orderBy('CreatedDate', 'desc')->get();
        $totalItems = Location::count();

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

        try {
            Location::create([
                'RowId' => $data['RowId'],
                'LocationCode' => $data['code'],
                'LocationName' => $data['name'],
                'IsLocation' => $data['is_location'],
                'IsProjectLocation' => $data['is_project_location'],
                'IsDefault' => isset($data['is_default']) ? true : false,
                'Status' => 'active',
                'Active' =>  true,
                'CreatedDate' => $data['CreatedDate'],
                // 'CreatedBy' => Auth::user()->name
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Location created successfully!'
            ], 201);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function update(LocationUpdateRequest $request, $id)
    {
        $data = $request->validated();
        $data['LastUpdatedDate'] = date('Y-m-d H:i:s');

        try {
            $location = Location::findOrFail($id);

            $location->LocationCode = $data['code'];
            $location->LocationName = $data['name'];
            $location->IsLocation = $data['is_location'];
            $location->IsProjectLocation = $data['is_project_location'];
            $location->IsDefault =  isset($data['is_default']) ? true : false;
            $location->Status = $data['status'] ? 'active' : 'inactive';
            $location->Active = $data['status'] ? true : false;
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
