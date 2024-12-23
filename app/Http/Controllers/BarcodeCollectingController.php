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
        $dataCollection = collect($data);

        if ($request->has('asset_code')) {
            $assetCode = $request->input('asset_code');
            $dataCollection = $dataCollection->filter(function ($item) use ($assetCode) {
                return str_contains($item->AssetCode, $assetCode);
            });
        }

        $currentPage = $request->input('current_page', 1);
        $perPage = $request->input('per_page', 10);

        $paginator = $this->paginateArray($dataCollection->toArray(), $perPage, $currentPage);
        $response = [
            'data' => $paginator->items(),
            'currentPage' => $paginator->currentPage(),
            'rowsPerPage' => $paginator->perPage(),
            'totalPages' => $paginator->total(),
            'lastPage' => $paginator->lastPage(),
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
