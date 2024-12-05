<?php

namespace App\Http\Controllers;

use App\Http\Requests\SubLocation\SubLocationStoreRequest;
use App\Http\Requests\SubLocation\SubLocationUpdateRequest;
use App\Models\SubLocation;
use Illuminate\Http\Request;

class SubLocationController extends Controller
{
    public function index(Request $request)
    {
        $page = $request->input('per_page', 10);
        $current_page = $request->input('current_page', 1);
        $search = $request->input('search');

        $subLocations = SubLocation::skip(($current_page - 1) * $page)->take($page)->orderBy('CreatedDate', 'desc')->get();
        $totalItems = SubLocation::count();

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

        try {
            SubLocation::create([
                'RowId' => $data['RowId'],
                'SubLocationCode' => $data['code'],
                'SubLocationName' => $data['name'],
                'Status' => 'active',
                'Active' =>  true,
                'CreatedDate' => $data['CreatedDate'],
                // 'CreatedBy' => Auth::user()->name
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Sub Location created successfully!'
            ], 201);
        } catch (\Throwable $th) {
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

            $subLocation->SubLocationCode = $data['code'];
            $subLocation->SubLocationName = $data['name'];
            $subLocation->Status = $data['status'] ? 'active' : 'inactive';
            $subLocation->Active = $data['status'] ? true : false;
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
