<?php

namespace App\Http\Controllers;

use App\Http\Requests\Project\ProjectStoreRequest;
use App\Http\Requests\Project\ProjectUpdateRequest;
use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        $page = $request->input('per_page', 10);
        $current_page = $request->input('current_page', 1);
        $search = $request->input('search');

        $projects = Project::skip(($current_page - 1) * $page)->take($page)->get();
        $totalItems = Project::count();

        $totalPages = ceil($totalItems / $page);

        $response = [
            'currentPage' => $current_page,
            'rowsPerPage' => $page,
            'totalPages' => $totalPages,
            'data' => $projects,
        ];

        return response()->json($response);
    }

    public function store(ProjectStoreRequest $request)
    {
        $data = $request->validated();

        $data['RowId'] = Project::getNextRowId();
        $data['CreatedDate'] = date('Y-m-d H:i:s');

        try {
            Project::create([
                'RowId' => $data['RowId'],
                'ProjectCode' => $data['code'],
                'ProjectName' => $data['name'],
                'Status' => 'active',
                'Active' =>  true,
                'CreatedDate' => $data['CreatedDate'],
                // 'CreatedBy' => Auth::user()->name
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Project created successfully!'
            ], 201);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function update(ProjectUpdateRequest $request, $id)
    {
        $data = $request->validated();
        $data['LastUpdatedDate'] = date('Y-m-d H:i:s');

        try {
            $project = Project::findOrFail($id);

            $project->ProjectCode = $data['code'];
            $project->ProjectName = $data['name'];
            $project->Status = $data['status'] ? 'active' : 'inactive';
            $project->Active = $data['status'] ? true : false;
            $project->LastUpdatedDate = $data['LastUpdatedDate'];

            $project->save();

            return response()->json([
                'success' => true,
                'message' => 'Project updated successfully!'
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
        $project = Project::findOrFail($id);
        $project->delete();

        return response()->json([
            'success' => true,
            'message' => 'Project deleted successfully!'
        ], 200);
    }
}
