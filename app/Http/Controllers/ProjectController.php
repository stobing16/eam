<?php

namespace App\Http\Controllers;

use App\Http\Requests\Project\ProjectStoreRequest;
use App\Http\Requests\Project\ProjectUpdateRequest;
use App\Models\Api\Counter;
use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        $page = $request->input('per_page', 10);
        $current_page = $request->input('current_page', 1);
        $search = $request->input('search');

        $query = Project::orderBy('ProjectName', 'asc');

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('ProjectName', 'like', "%$search%");
            });
        }

        $totalItems = $query->count();
        $projects = $query
            ->skip(($current_page - 1) * $page)
            ->take($page)
            ->get();

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
        $data['CreatedDate'] = date('Y-m-d H:i:s');

        try {

            $ctrFormat = str_replace([' ', '-'], '', $data['name']);
            $ctrFormat = (strlen($ctrFormat) > 2) ? strtoupper(substr($ctrFormat, 0, 2)) : $data['name'];

            $counter = Counter::where('CounterId', 'PR')->first();
            if ($counter) {
                $counter->CounterNumber++;
                $counter->save();

                $count = $counter->CounterNumber;
            } else {
                $count = 1;
            }

            $code = $ctrFormat . $count;

            Project::create([
                'ProjectCode' => $code,
                'ProjectName' => $data['name'],
                'CreatedDate' => $data['CreatedDate'],
                // 'CreatedBy' => Auth::user()->name,
                'CreatedBy' => '1',
                'LastUpdatedBy' => "1"
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

            $project->ProjectName = $data['name'];
            $project->Status = $data['status'];
            $project->Active = $data['status'] === 'A' ? 1 : 0;
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
