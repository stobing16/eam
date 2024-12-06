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
        $current_page = $request->input('current_page', 1);
        $search = $request->input('search');

        $employees = Employee::skip(($current_page - 1) * $page)->take($page)->orderBy('CreatedDate', 'desc')->get();
        $totalItems = Employee::count();

        $totalPages = ceil($totalItems / $page);

        $response = [
            'currentPage' => $current_page,
            'rowsPerPage' => $page,
            'totalPages' => $totalPages,
            // 'isFirstPage' => $employees->onFirstPage(),
            // 'isLastPage' => $employees->onLastPage(),
            'data' => $employees,
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
                'Nama' => $data['Nama'],
                'NIK' => 0,
                'Email' => $data['Email'],
                'Department' => $data['Department'],
                'Jabatan' => $data['Jabatan'],
                'Status' => $data['Status'],
                'Active' => 1,
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
                    'NIK' =>0,
                    'Email' => $value['email'],
                    'Jabatan' => $value['jabatan'],
                    'Status' => $value['status'] ? "A" : "I",
                    'Active' => $value['status'],
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
//        'RowId' => $data['RowId'],
//                'Nama' => $data['Nama'],
//                'NIK' => 123,
//                'Email' => $data['Email'],
//                'Department' => $data['Department'],
//                'Jabatan' => $data['Jabatan'],
//                'Status' => $data['Status'],
//                'Active' => 1,
//                'CreatedDate' => $data['CreatedDate'],
        try {
            $employee = Employee::findOrFail($id);

            $employee->Nama = $data['Nama'];
            $employee->NIK = 0;
            $employee->Email = $data['Email'];
            $employee->Jabatan = $data['Jabatan'];
            $employee->Status = $data['Status'];
            $employee->Active = $data['Status'] === 'A' ? 1 : 0;
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
