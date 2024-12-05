<?php

namespace App\Http\Controllers;

use App\Http\Requests\Supplier\SupplierStoreRequest;
use App\Http\Requests\Supplier\SupplierUpdateRequest;
use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        $page = $request->input('per_page', 10);
        $current_page = $request->input('current_page', 1);
        $search = $request->input('search');

        $suppliers = Supplier::skip(($current_page - 1) * $page)->take($page)->get();
        $totalItems = Supplier::count();

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

        $data['RowId'] = Supplier::getNextRowId();
        $data['CreatedDate'] = date('Y-m-d H:i:s');

        try {
            Supplier::create([
                'RowId' => $data['RowId'],
                'SupplierCode' => $data['code'],
                'SupplierName' => $data['name'],
                'Phone' => $data['phone'],
                'Mobile' => $data['mobile'],
                'Address' => $data['address'],
                'Status' => 'active',
                'Active' =>  true,
                'CreatedDate' => $data['CreatedDate'],
                // 'CreatedBy' => Auth::user()->name
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Supplier created successfully!'
            ], 201);
        } catch (\Throwable $th) {
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

            $supplier->SupplierCode = $data['code'];
            $supplier->SupplierName = $data['name'];
            $supplier->Phone = $data['phone'];
            $supplier->Mobile = $data['mobile'];
            $supplier->Address = $data['address'];

            $supplier->Status = $data['status'] ? 'active' : 'inactive';
            $supplier->Active = $data['status'] ? true : false;
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
