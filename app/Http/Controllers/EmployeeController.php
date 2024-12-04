<?php

namespace App\Http\Controllers;

use App\Http\Requests\EmployeeStoreBatchRequest;
use App\Http\Requests\EmployeeStoreRequest;
use App\Http\Requests\EmployeeUpdateRequest;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EmployeeController extends Controller
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

    public function jabatan()
    {
        $query = DB::select('SELECT DISTINCT Jabatan FROM MsEmployees');
        return response()->json([
            'success' => true,
            'message' => '',
            'data' => $query
        ]);
    }

    public function store(EmployeeStoreRequest $request)
    {
        $data = $request->validated();

        $data['RowId'] = Employee::getNextRowId();
        $data['CreatedDate'] = date('Y-m-d H:i:s');

        try {
            Employee::create([
                'RowId' => $data['RowId'],
                'Nama' => $data['name'],
                'NIK' => $data['nik'],
                'Email' => $data['email'],
                'Jabatan' => $data['jabatan'],
                'Status' => $data['status'],
                'CreatedDate' => $data['CreatedDate'],
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Employee created successfully!'
            ], 201);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function storeExcel(EmployeeStoreBatchRequest $request)
    {
        $data = $request->validated();

        try {
            foreach ($data as $value) {
                $value['RowId'] = Employee::getNextRowId();
                $value['CreatedDate'] = date('Y-m-d H:i:s');

                Employee::create([
                    'RowId' => $value['RowId'],
                    'Nama' => $value['name'],
                    'NIK' => $value['nik'],
                    'Email' => $value['email'],
                    'Jabatan' => $value['jabatan'],
                    'Status' => $value['status'],
                    'CreatedDate' => $value['CreatedDate'],
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Employee created successfully!'
            ], 201);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function update(EmployeeStoreRequest $request, $id)
    {
        $data = $request->validated();
        $data['LastUpdatedDate'] = date('Y-m-d H:i:s');

        try {
            $employee = Employee::findOrFail($id);

            $employee->Nama = $data['name'];
            $employee->NIK = $data['nik'];
            $employee->Email = $data['email'];
            $employee->Jabatan = $data['jabatan'];
            $employee->Status = $data['status'];
            $employee->LastUpdatedDate = $data['LastUpdatedDate'];

            $employee->save();

            return response()->json([
                'success' => true,
                'message' => 'Employee updated successfully!'
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
        $employee = Employee::findOrFail($id);
        $employee->delete();

        return response()->json([
            'success' => true,
            'message' => 'Employee deleted successfully!'
        ], 200);
    }
}
