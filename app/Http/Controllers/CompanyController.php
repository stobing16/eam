<?php

namespace App\Http\Controllers;

use App\Http\Requests\Company\CompanyStoreRequest;
use App\Http\Requests\Company\CompanyUpdateRequest;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CompanyController extends Controller
{
    public function index(Request $request)
    {
        $page = $request->input('per_page', 10);
        $current_page = $request->input('current_page', 1);
        $search = $request->input('search');

        $companies = Company::skip(($current_page - 1) * $page)->take($page)->get();
        $totalItems = Company::count();

        $totalPages = ceil($totalItems / $page);

        $response = [
            'currentPage' => $current_page,
            'rowsPerPage' => $page,
            'totalPages' => $totalPages,
            'data' => $companies,
        ];

        return response()->json($response);
    }

    public function store(CompanyStoreRequest $request)
    {
        $data = $request->validated();

        $data['RowId'] = Company::getNextRowId();
        $data['CreatedDate'] = date('Y-m-d H:i:s');

        try {
            Company::create([
                'RowId' => $data['RowId'],
                'CompanyId' => $data['code'],
                'CompanyName' => $data['name'],
                'Status' => 'active',
                'Active' =>  true,
                'CreatedDate' => $data['CreatedDate'],
                // 'CreatedBy' => Auth::user()->name
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Company created successfully!'
            ], 201);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function update(CompanyUpdateRequest $request, $id)
    {
        $data = $request->validated();
        $data['LastUpdatedDate'] = date('Y-m-d H:i:s');

        try {
            $company = Company::findOrFail($id);

            $company->CompanyId = $data['code'];
            $company->CompanyName = $data['name'];
            $company->Status = $data['status'] ? 'active' : 'inactive';
            $company->Active = $data['status'] ? true : false;
            $company->LastUpdatedDate = $data['LastUpdatedDate'];

            $company->save();

            return response()->json([
                'success' => true,
                'message' => 'Company updated successfully!'
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
        $company = Company::findOrFail($id);
        $company->delete();

        return response()->json([
            'success' => true,
            'message' => 'Company deleted successfully!'
        ], 200);
    }
}
