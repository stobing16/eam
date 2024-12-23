<?php

namespace App\Http\Controllers\Api\Transaction;

use App\Http\Controllers\Controller;
use App\Models\Api\Counter;
use App\Models\Api\TxOpnameOrder;
use App\Models\Location;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OpnameController extends Controller
{
    public function index(Request $request)
    {
        $page = (int) $request->input('per_page', 10);
        $current_page = (int) $request->input('current_page', 1);
        $search = $request->input('search', '');

        $query = TxOpnameOrder::with('location', 'opnameAndroids')->orderBy('CreatedDate', 'desc');

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('Location', 'like', "%$search%")
                    ->orWhere('Email', 'like', "%$search%");
            });
        }

        $totalItems = $query->count();


        $opname_orders = $query
            ->skip(($current_page - 1) * $page)
            ->take($page)
            ->get();

        foreach ($opname_orders as $key => $item) {
            $item->StatusResult = $this->filterOpnameStatus($item->Status, $item->Active, count($item->opnameAndroids));
            $item->OpnameOrderDate = Carbon::parse($item->OpnameOrderDate)->format('j F Y');
        }

        $totalPages = ceil($totalItems / $page);

        $response = [
            'currentPage' => $current_page,
            'rowsPerPage' => $page,
            'totalPages' => $totalPages,
            'data' => $opname_orders,
        ];

        return response()->json($response);
    }

    protected function filterOpnameStatus($status, $active, $count)
    {
        $result = 'Inactive';

        switch (true) {
            case ($status == 'A' && $active == '1' && $count == 0):
                $result = 'Open';
                break;
            case ($status == 'A' && $active == '1' && $count > 0):
                $result = 'On Progress';
                break;
            case ($status == '2' && $active == '1'):
                $result = 'Closed';
                break;
        }

        return $result;
    }

    public function details(Request $request, $id)
    {
        $query = TxOpnameOrder::with('location', 'opnameAndroids')->orderBy('CreatedDate', 'desc')->Where('RowId', $id)->first();
        $query->StatusResult = $this->filterOpnameStatus($query->Status, $query->Active, count($query->opnameAndroids));
        $query->OpnameOrderDate = Carbon::parse($query->OpnameOrderDate)->format('j F Y');

        $page = (int) $request->input('per_page', 10);
        $current_page = (int) $request->input('current_page', 1);
        $search = $request->input('search', '');

        $results = DB::select('EXEC GetOpnameOrderDetailList @OpnameOrderId = ?', [$id]);
        $collection = collect($results);

        $totalItems = $collection->count();

        if (!empty($search)) {
            $collection->where(function ($q) use ($search) {
                $q->where('Location', 'like', "%$search%");
            });
        }

        $details = $collection
            ->skip(($current_page - 1) * $page)
            ->take($page);

        $totalPages = ceil($totalItems / $page);

        foreach ($details as $key => $detail) {
            $detail->CreatedDate = Carbon::parse($query->CreatedDate)->format('j F Y');
        }

        $response = [
            'opname' => $query,
            'details' => [
                'currentPage' => $current_page,
                'rowsPerPage' => $page,
                'totalPages' => $totalPages,
                'data' => $details,
            ]
        ];
        return response()->json($response);
    }

    public function getLocationList()
    {
        $loc = Location::select('RowId', 'LocationCode', 'LocationName')->get();
        return response()->json($loc);
    }

    public function saveOpname(Request $request)
    {
        $request->validate([
            'location' => 'required',
            'date' => 'required',
            'category' => 'required',
        ]);

        DB::beginTransaction();
        try {
            $format = 'OO';
            $counter = Counter::where('CounterId', $format)->firstOrFail();
            $counter->CounterNumber = $counter->CounterNumber + 1;

            $opname_order_id = $request->category . $counter->CounterNumber;
            $counter->save();

            TxOpnameOrder::create([
                'OpnameOrderId' => $opname_order_id,
                'OpnameOrderDate' => date($request->date),
                'OpnameOrderType' => $request->category,
                'LocationCode' => $request->location,
                // 'CreatedBy' => Auth::user()->id,
                'CreatedBy' => "yocky",
                'CreatedDate' => date('Y-m-d H:i:s'),
            ]);

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Data Opname created successfully!'
            ], 201);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function updateOpname(Request $request, $id)
    {
        $request->validate([
            'location' => 'required',
            'category' => 'required',
            'status' => 'required',
        ]);


        $opname = TxOpnameOrder::where('RowId', $id)->firstOrFail();

        $opname->OpnameOrderType = $request->category;
        $opname->LocationCode = $request->location;
        $opname->Status = $request->status;
        $opname->Active = $request->status === 'A' ? 1 : 0;
        $opname->LastUpdatedDate = date('Y-m-d H:i:s');

        $opname->save();

        return response()->json([
            'success' => true,
            'message' => 'Data Opname updated successfully!'
        ], 201);
    }
}
