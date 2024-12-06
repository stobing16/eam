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
         return view('employee');
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
