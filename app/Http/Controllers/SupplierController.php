<?php

namespace App\Http\Controllers;

use App\Http\Requests\Supplier\SupplierStoreRequest;
use App\Http\Requests\Supplier\SupplierUpdateRequest;
use App\Models\Api\Counter;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        $page = (int) $request->input('per_page', 10);
        $current_page = (int) $request->input('current_page', 1);
        $search = $request->input('search', '');

        $query = Supplier::orderBy('SupplierName', 'asc');

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('SupplierName', 'like', "%$search%");
            });
        }

        $totalItems = $query->count();

        $suppliers = $query
            ->skip(($current_page - 1) * $page)
            ->take($page)
            ->get();

        $totalPages = ceil($totalItems / $page);
        $response = [
            'currentPage' => $current_page,
            'rowsPerPage' => $page,
            'totalPages' => $totalPages,
            'data' => $suppliers,
        ];

        return response()->json($response);
    }

    public function store(SupplierStoreRequest $request)
    {
        $data = $request->validated();

        $data['CreatedDate'] = date('Y-m-d H:i:s');

        DB::beginTransaction();
        try {
            $ctrFormat = str_replace([' ', '-'], '', $data['name']);
            $ctrFormat = (strlen($ctrFormat) > 2) ? strtoupper(substr($ctrFormat, 0, 2)) : $data['name'];

            $counter = Counter::where('CounterId', 'SP')->first();
            if ($counter) {
                $counter->CounterNumber++;
                $counter->save();

                $count = $counter->CounterNumber;
            } else {
                $count = 1;
            }

            $code = $ctrFormat . $count;

            Supplier::create([
                'SupplierCode' => $code,
                'SupplierName' => $data['name'],
                'Phone' => $data['phone'],
                'Mobile' => $data['mobile'],
                'Address' => $data['address'],
                'CreatedDate' => $data['CreatedDate'],
                // 'CreatedBy' => Auth::user()->name
                'CreatedBy' => "1",
                'LastUpdatedBy' => "1",
            ]);

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Supplier created successfully!'
            ], 201);
        } catch (\Throwable $th) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function update(SupplierUpdateRequest $request, $id)
    {
        $data = $request->validated();
        $data['LastUpdatedDate'] = date('Y-m-d H:i:s');

        try {
            $supplier = Supplier::findOrFail($id);

            $supplier->SupplierName = $data['name'];
            $supplier->Phone = $data['phone'];
            $supplier->Mobile = $data['mobile'];
            $supplier->Address = $data['address'];

            $supplier->Status = $data['status'];
            $supplier->Active = $data['status'] === 'A' ? 1 : 0;
            $supplier->LastUpdatedDate = $data['LastUpdatedDate'];

            $supplier->save();

            return response()->json([
                'success' => true,
                'message' => 'Supplier updated successfully!'
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
        $supplier = Supplier::findOrFail($id);
        $supplier->delete();

        return response()->json([
            'success' => true,
            'message' => 'Company deleted successfully!'
        ], 200);
    }
}
