<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;

class BarcodeCollectingController extends Controller
{
    public function index(Request $request)
    {
        $data = DB::select('EXEC BarcodeCollectingReport');

        $currentPage = $request->input('page', 1);
        $perPage = $request->input('per_page', 10);

        $paginator = $this->paginateArray($data, $perPage, $currentPage);
        $response = [
            'data' => $paginator->items(),
            'current_page' => $paginator->currentPage(),
            'per_page' => $paginator->perPage(),
            'total' => $paginator->total(),
            'last_page' => $paginator->lastPage(),
            'from' => $paginator->firstItem(),
            'to' => $paginator->lastItem(),
        ];

        return response()->json($response);
    }

    private function paginateArray(array $items, int $perPage, int $currentPage)
    {
        $offset = ($currentPage - 1) * $perPage;

        $paginatedItems = array_slice($items, $offset, $perPage);

        return new LengthAwarePaginator(
            $paginatedItems,
            count($items),
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'query' => request()->query()]
        );
    }
}
