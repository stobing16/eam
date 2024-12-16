<?php

namespace App\Http\Controllers;

use App\Http\Requests\Company\CompanyStoreRequest;
use App\Http\Requests\Company\CompanyUpdateRequest;
use App\Models\Api\Counter;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CompanyController extends Controller
{
    public function index(Request $request)
    {
        $page = (int) $request->input('per_page', 10);
        $current_page = (int) $request->input('current_page', 1);
        $search = $request->input('search', '');

        $query = Company::orderBy('CreatedDate', 'desc');

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('CompanyName', 'like', "%$search%");
            });
        }

        $totalItems = $query->count();

        $companies = $query
            ->skip(($current_page - 1) * $page)
            ->take($page)
            ->get();

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

        DB::beginTransaction();
        try {
            $ctrFormat = str_replace([' ', '-'], '', $data['name']);
            $ctrFormat = (strlen($ctrFormat) > 2) ? strtoupper(substr($ctrFormat, 0, 2)) : $data['name'];

            $counter = Counter::where('CounterId', 'CP')->first();
            if ($counter) {
                $counter->CounterNumber++;
                $counter->save();

                $count = $counter->CounterNumber;
            } else {
                $count = 1;
            }

            $code = $ctrFormat . $count;

            Company::create([
                'CompanyId' => $code,
                'CompanyName' => $data['name'],
                'CreatedDate' => $data['CreatedDate'],
                // 'CreatedBy' => Auth::user()->id,
                'CreatedBy' => '1',
                'LastUpdatedBy' => "1"
            ]);

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Company created successfully!'
            ], 201);
        } catch (\Throwable $th) {
            DB::rollBack();
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

            $company->CompanyName = $data['name'];
            $company->Status = $data['status'];
            $company->Active = $data['status'] === 'A' ? 1 : 0;
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
