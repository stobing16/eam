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
        $page = (int) $request->input('per_page', 10);
        $current_page = (int) $request->input('current_page', 1);
        $search = $request->input('search', '');

        $query = Employee::orderBy('CreatedDate', 'desc');

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('Nama', 'like', "%$search%")
                    ->orWhere('Email', 'like', "%$search%");
            });
        }

        $totalItems = $query->count();

        $employees = $query
            ->skip(($current_page - 1) * $page)
            ->take($page)
            ->get();

        $totalPages = ceil($totalItems / $page);

        $response = [
            'currentPage' => $current_page,
            'rowsPerPage' => $page,
            'totalPages' => $totalPages,
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

    public function client()
    {
        $query = DB::select('SELECT * FROM MsClient');
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
                'Nama' => $data['nama'],
                'NIK' => $data['nik'],
                'Jabatan' => $data['jabatan'],
                'NPWP' => $data['npwp'],
                'PTKP' => $data['ptkp'],
                'JoinDate' => $data['join_date'],
                'NoRek' => $data['no_rek'],
                'NamaRek' => $data['nama_rek'],
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

        DB::beginTransaction();
        $duplicated_data = [];

        try {
            foreach ($data as $value) {
                // CHECK IF DATA WITH THIS NIK EXISTS
                $isEmployeeExists = Employee::where('NIK', $value['nik'])->exists();
                if ($isEmployeeExists) {
                    $duplicated_data[] = [
                        'nik' => $value['nik'],
                        'nama' => $value['nama'],
                    ];
                };

                $value['RowId'] = Employee::getNextRowId();
                $value['CreatedDate'] = date('Y-m-d H:i:s');

                Employee::create([
                    'RowId' => $data['RowId'],
                    'Nama' => $data['nama'],
                    'NIK' => isset($data['nik']) ? $data['nik'] : "###",
                    'Jabatan' => $data['jabatan'],
                    'NPWP' => $data['npwp'],
                    'PTKP' => $data['ptkp'],
                    'JoinDate' => $data['join_date'],
                    'NoRek' => $data['no_rek'],
                    'NamaRek' => $data['nama_rek'],
                    'CreatedDate' => $data['CreatedDate'],
                ]);
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Employee created successfully!',
                'additional_data' => $duplicated_data
            ], 201);
        } catch (\Throwable $th) {
            DB::rollBack();
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

            $employee->Nama = $data['Nama'];
            $employee->NIK = 0;
            $employee->Email = $data['Email'];
            $employee->Jabatan = $data['Jabatan'];
            $employee->Department = $data['Department'];
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
