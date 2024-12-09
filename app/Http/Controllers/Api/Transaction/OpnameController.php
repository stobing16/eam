<?php

namespace App\Http\Controllers\Api\Transaction;

use App\Http\Controllers\Controller;
use App\Models\Api\Counter;
use App\Models\Api\TxOpnameOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OpnameController extends Controller
{
    public function index()
    {
        $results = DB::select('EXEC GetOpnameOrderList');
        return response()->json($results);
    }

    public function details($id)
    {
        $results = DB::select('EXEC GetOpnameOrderDetailList @OpnameOrderId = ?', [$id]);
        return response()->json($results);
    }

    public function saveOpname(Request $request)
    {
        $request->validate([
            'opname_order_date' => 'required',
            'opname_order_type' => 'required',
            'location_code' => 'required',
            'status' => 'required',
        ]);

        DB::beginTransaction();
        try {
            $format = 'OO';
            $counter = Counter::select('CounterNumber')->where('CounterId', $format)->firstOrFail();
            $counter->CounterNumber = $counter->CounterNumber + 1;

            $opname_order_id = $request->opname_order_type . $counter->CounterNumber;
            $counter->save();

            TxOpnameOrder::create([
                'RowId' => TxOpnameOrder::getNextRowId(),
                'OpnameOrderId' => $opname_order_id,
                'OpnameOrderDate' => date($request->opname_order_date),
                'OpnameOrderType' => $request->opname_order_type,
                'LocationCode' => $request->location_code,
                'Status' => $request->status,
                'Active' => 1,
                'CreatedBy' => Auth::user()->id,
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
            'opname_order_date' => 'required',
            'opname_order_type' => 'required',
            'location_code' => 'required',
            'status' => 'required',
        ]);

        $opname = TxOpnameOrder::where('OpnameOrderId', $id)->firstOrFail();
        $opname->OpnameOrderDate = date($request->opname_order_date);
        $opname->OpnameOrderType = $request->opname_order_type;
        $opname->Active = $request->status;

        $opname->save();

        return response()->json([
            'success' => true,
            'message' => 'Data Opname updated successfully!'
        ], 201);
    }
}
