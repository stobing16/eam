<?php

namespace App\Http\Controllers\Api\Transaction;

use App\Http\Controllers\Controller;
use App\Models\Api\Asset;
use App\Models\Api\AssetType;
use App\Models\Api\Counter;
use App\Models\Api\ModelAsset;
use App\Models\Api\SudahPrint;
use App\Models\Api\TxCheckIn;
use App\Models\Api\TxCheckOut;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AssetController extends Controller
{

    public function index()
    {
        $results = DB::select('EXEC GetAssetList');
        return response()->json($results);
    }

    public function saveAsset(Request $request)
    {
        $request->validate([
            'company_code' => 'required',
            'model_code' => 'required',
            'category_code' => 'required',
            'condition' => 'required',
            'serial_number' => 'required',
            'name' => 'required|string|max:255',
            'purchase_date' => 'required',
            'supplier_code' => 'required',
            'order_number' => 'required',
            'purchase_cost' => 'required',
            'warranty' => 'required|numeric',
            'received_by' => 'required',
            'location_code' => 'required',
            'qty' => 'required|numeric',
            'notes' => 'nullable',
        ]);


        DB::beginTransaction();
        try {
            $is_asset_exists = Asset::where('AssetCode', $request->asset_code)->exists();
            // Jika Asset sudah ada, maka update data
            if ($is_asset_exists) {
                DB::table('MsAsset')
                    ->where('AssetCode', $request->asset_code)
                    ->update([
                        'Condition' => $request->condition,
                        'SerialNumber' => $request->serial_number,
                        'AssetName' => $request->asset_name,
                        'AssetCategoryCode' => $request->category_code,
                        'PurchaseDate' => $request->purchase_date,
                        'SupplierCode' => $request->supplier_code,
                        'OrderNumber' => $request->order_number,
                        'Warranty' => $request->warranty,
                        'Notes' => $request->notes,
                        'Status' => $request->status ? 'A' : 'I',
                        'Active' => $request->status,
                        'ReceivedBy' => $request->received_by,
                        'LastUpdatedBy' => Auth::user()->id,
                        'LastUpdatedDate' => date('Y-m-d H:i:s'),
                    ]);

                DB::commit();
                return response()->json([
                    'success' => true,
                    'message' => 'Asset updated successfully!'
                ], 201);
            }
            // Jika Tidak, Maka insert data baru
            else {
                $count = 0;

                while ($count < $request->qty) {
                    $assetTypeCode = ModelAsset::select('AssetTypeCode')->where('ModelCode', $request->model_code)->firstOrFail();
                    $alias = AssetType::select('Alias')->where('AssetTypeCode', $assetTypeCode)->firstOrFail();

                    $year = '/' . date('Y');
                    $month = date('m');
                    $month = (strlen($month) == 1) ? '/0' . $month : '/' . $month;

                    // GENERATE COUNTER NUMBER BARU TIAP TAHUN NYA
                    $assetCodeBase = $request->company_code . '/' . $alias . $month . $year;
                    $counter = Counter::select(['CounterNumber', 'Other'])->where('CounterId', 'AST')->firstOrFail();

                    $iCtr = $counter->CounterNumber;
                    $yearCounter = $counter->Other;

                    // Cek apakah tahun counter berubah
                    if ($yearCounter != str_replace('/', '', $year)) {
                        $iCtr = 0;
                        $iCtr++;
                        $counter->CounterNumber = $iCtr;
                        $counter->Other = str_replace('/', '', $year);
                    } else {
                        $iCtr++;
                        $counter->CounterNumber = $iCtr;
                    }

                    $counter->save();
                    // END GENERATE COUNTER NUMBER

                    // GENERATE ASSET CODE
                    $paramLen = 5;
                    if (strlen($iCtr) == $paramLen) {
                        $AssetCode = $assetCodeBase . '/' . $iCtr;
                    } else {
                        $iNol = str_pad($iCtr, $paramLen, '0', STR_PAD_LEFT);
                        $AssetCode = $assetCodeBase . '/' . $iNol;
                    }
                    // END GENERATE ASSET CODE

                    // SAVE ASSET
                    Asset::create([
                        'RowId' =>  Asset::getNextRowId(),
                        'CompanyCode' => $request->company_code,
                        'ModelCode' => $request->model_code,
                        'Condition' => $request->condition,
                        'AssetBarcode' => $AssetCode,
                        'AssetCode' => $AssetCode,
                        'SerialNumber' => $request->serial_number,
                        'AssetName' => $request->asset_name,
                        'AssetCategoryCode' => $request->category_code,
                        'PurchaseDate' => $request->purchase_date,
                        'SupplierCode' => $request->supplier_code,
                        'OrderNumber' => $request->order_number,
                        'PurchaseCost' => $request->purchase_cost,
                        'Warranty' => $request->warranty,
                        'Notes' => $request->notes,
                        'LocationCode' => $request->location_code,
                        'Status' => $request->status ? 'A' : 'I',
                        'Active' => $request->status,
                        'CreatedBy' => Auth::user()->id,
                        'ReceivedBy' => $request->received_by,
                        'CreatedDate' => date('Y-m-d H:i:s'),
                        // 'LastUpdatedBy' => '',
                        // 'LastUpdatedDate' => '1900-01-01',
                        // 'AssetImage' => $request['ImageFile'],
                    ]);
                    // END SAVE ASSET

                    SudahPrint::create([
                        'Barcode' => $AssetCode,
                        'CreatedDate' => date('Y-m-d H:i:s'),
                        'Status' => 0
                    ]);

                    $check_in_code = 'CKI';

                    $counterCKI = Counter::select('CounterNumber')->where('CounterId', $check_in_code)->firstOrfail();
                    $counterCKI->CountNumber = $counterCKI->CountNumber + 1;

                    $check_in_code = $counterCKI->CountNumber;
                    $counterCKI->save();

                    TxCheckIn::create([
                        'RowId' => TxCheckIn::getNextRowId(),
                        'CheckInCode' => $check_in_code,
                        'CheckOutCode' => '',
                        'AssetCode' => $AssetCode,
                        'Condition' => $request->condition,
                        'CheckInDate' => date('Y-m-d H:i:s'),
                        'Notes' => $request->notes,
                        'Active' => 'A',
                        'Status' => true,
                        'CreatedBy' => Auth::user()->id,
                        'CreatedDate' => date('Y-m-d H:i:s'),
                        'DeliveredBy' => '',
                        'ReceivedBy' => $request->received_by,
                        'CheckInLocation' => $request->location_code
                    ]);

                    $count++;
                }
                DB::commit();
                return response()->json([
                    'success' => true,
                    'message' => 'Asset created successfully!'
                ], 201);
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function updateAsset(Request $request) {}

    public function checkIn(Request $request)
    {
        $request->validate([
            'checkout_code' => 'required|string|max:50',
            'asset_code' => 'required|string|max:50',
            'checkin_date' => 'required',
            'condition' => 'required|string|max:20',
            'notes' => 'nullable|string|max:200',  // Allowing null for optional Notes
            'delivered_by' => 'required|string|max:20',
            'received_by' => 'required|string|max:20',
            'checkin_location' => 'required|string|max:50',
        ]);

        DB::beginTransaction();

        try {
            $format = 'CKI';
            $counter = Counter::select('CounterNumber')->where('CounterId', $format)->firstOrFail();
            $counter->CounterNumber = $counter->CounterNumber + 1;

            $check_in_code = $format . $counter->CounterNumber;
            $counter->save();

            TxCheckIn::create([
                'RowId' => TxCheckIn::getNextRowId(),
                'CheckInCode' => $check_in_code,
                'CheckOutCode' => $request->checkout_code,
                'AssetCode' => $request->asset_code,
                'Condition' => $request->condition,
                'CheckInDate' => date($request->checkin_date),
                'Notes' => $request->notes,
                'Active' => 'A',
                'Status' => true,
                'CreatedBy' => Auth::user()->id,
                'CreatedDate' => date('Y-m-d H:i:s'),
                'LastUpdatedDate' => date('Y-m-d H:i:s'),
                'DeliveredBy' => $request->delivered_by,
                'ReceivedBy' => $request->received_by,
                'CheckInLocation' => $request->checkin_location
            ]);

            $asset = Asset::where('AssetCode', $request->asset_code)->firstOrFail();
            $asset->LocationCode = $request->checkin_location;
            $asset->Condition = $request->condition;
            $asset->save();

            $checkout = TxCheckOut::where('CheckOutCode', $request->checkout_code)->firstOrFail();
            $checkout->Status = 'CI';
            $checkout->save();

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Check In Asset created successfully!'
            ], 201);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function checkOut(Request $request)
    {
        $request->validate([
            'checkout_code' => 'required|string|max:50',
            'asset_code' => 'required|string|max:50',
            'checkout_to' => 'required|string|max:50',
            'checkout_date' => 'required',
            'expected_checkin' => 'required|string|max:20', // Assuming this is a string
            'project_code' => 'required|string|max:50',
            'project_location_code' => 'required|string|max:50',
            'notes' => 'nullable|string', // Nullable, if not provided it's fine
            'user_code' => 'required|string|max:20',
            'checkout_by' => 'required|string|max:20',
            'delivered_by' => 'required|string|max:20',
            'sub_location' => 'required|string|max:20',
            'acknowledge_by' => 'nullable|string|max:50', // Nullable, may not always be provided
        ]);

        DB::beginTransaction();
        try {
            $format = 'CKO';
            $counter = Counter::select('CounterNumber')->where('CounterId', $format)->firstOrFail();
            $counter->CounterNumber = $counter->CounterNumber + 1;

            $check_out_code = $format . $counter->CounterNumber;
            $counter->save();


            TxCheckOut::create([
                'RowId' => TxCheckOut::getNextRowId(),
                'CheckOutCode' => $check_out_code,
                'AssetCode' => $request->asset_code,
                'CheckOutDate' => date($request->checkout_date),
                'ExpectedCheckIn' => isset($request->expected_checkin) ? date($request->expected_checkin) : '1900-01-01',
                'ProjectCode' => $request->project_code,
                'ProjectLocationCode' => $request->project_location_code,
                'Notes' => $request->notes,
                'Active' => 'A',
                'Status' => true,
                'CreatedBy' => Auth::user()->id,
                'CreatedDate' => date('Y-m-d H:i:s'),
                'LastUpdatedDate' => date('Y-m-d H:i:s'),
                'CheckoutBy' => $request->checkout_by,
                'DeliveredBy' => $request->delivered_by,
                'SubLocation' => $request->sub_location,
                'AcknowledgeBy' => $request->acknowledge_by
            ]);


            $asset = Asset::where('AssetCode', $request->asset_code)->firstOrFail();
            $asset->LocationCode = $request->project_location_code;
            $asset->Status = 'CO';
            $asset->save();

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Check Out created successfully!'
            ], 201);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }
}
