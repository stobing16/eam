<?php

namespace App\Http\Controllers;

use App\Http\Requests\EmployeeStoreRequest;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $page = $request->input('per_page', 10);
        $employees = Employee::paginate($page);

        $response = [
            'currentPage' => $employees->currentPage(),
            'rowsPerPage' => $employees->perPage(),
            'totalPages' => $employees->lastPage(),
            'isFirstPage' => $employees->onFirstPage(),
            'isLastPage' => $employees->onLastPage(),
            'data' => $employees->items(),
        ];

        return response()->json($response);
    }

    public function store(EmployeeStoreRequest $request)
    {
        $validator = $request->validated();

        $validator['RowId'] = Employee::getNextRowId();
        $validator['CreatedDate'] = date('Y-m-d H:i:s');

        Employee::create([
            'RowId' => $validator['RowId']
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Employee created successfully!'
        ], 201);
    }
}
