<?php

namespace App\Http\Controllers\Api\Transaction;

use App\Http\Controllers\Controller;
use App\Models\Api\Asset;
use App\Models\Api\AssetType;
use App\Models\Api\Counter;
use App\Models\Api\ModelAsset;
use App\Models\Api\PickList;
use App\Models\Api\SudahPrint;
use App\Models\Api\TxCheckIn;
use App\Models\Api\TxCheckOut;
use App\Models\Company;
use App\Models\Employee;
use App\Models\Location;
use App\Models\Project;
use App\Models\SubLocation;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AssetController extends Controller
{
    public function index(Request $request)
    {
        $page = (int) $request->input('per_page', 10);
        $current_page = (int) $request->input('current_page', 1);
        $search = $request->input('search', '');

        // $data = $this->getAssetList();

        $data = Asset::whereHas('assetStatus', function ($query) {
            $query->whereIn('ChildId', ['A', 'CO']);
        })->with([
            'assetModel.brand',
            'txCheckout.createdPerson',
            'txCheckout.txCheckIn',
            'company' => function ($query) {
                $query->select('CompanyId', 'CompanyName');
            },
            'supplier' => function ($query) {
                $query->select('SupplierCode', 'SupplierName');
            },
            'location' => function ($query) {
                $query->select('LocationCode', 'LocationName');
            },
            'assetStatus' => function ($query) {
                $query->select('ChildId', 'PlDescription')->whereIn('ChildId', ["A", "CO"]);
            },
            'assetCondition' => function ($query) {
                $query->select('ChildId', 'PlDescription');
            }
        ]);

        if (!empty($search)) {
            $data->where(function ($q) use ($search) {
                $q->where('AssetName', 'like', "%$search%")
                    ->orWhere('AssetCode', 'like', "%$search%");
            });
        }
        $totalItems = $data->count();

        $asset = $data
            ->skip(($current_page - 1) * $page)
            ->take($page)
            ->get();

        $totalPages = ceil($totalItems / $page);

        $response = [
            'currentPage' => $current_page,
            'rowsPerPage' => $page,
            'totalPages' => $totalPages,
            'data' => $asset,
        ];

        return response()->json($response);
    }

    public function getFormDataList(Request $request)
    {
        $companies = Company::select('RowId', 'CompanyName', 'CompanyId', 'Active')->where('Active', 1)->get();
        $condition = PickList::select('RowId', 'ChildId', 'PlDescription', 'ParentId')->where('ParentId', 2)->get();
        $model = ModelAsset::with('brand')->get();
        $supplier = Supplier::select('RowId', 'SupplierCode', 'SupplierName')->get();
        $userReceived = Employee::select('RowId', 'NIK', 'Nama')->get();
        $location = Location::select('RowId', 'LocationCode', 'LocationName')->get();
        $subLocation = SubLocation::select('RowId', 'SubLocationCode', 'SubLocationName')->get();
        $employee = Employee::select('RowId', 'NIK', 'Nama')->get();
        $project = Project::select('RowId', 'ProjectCode', 'ProjectName')->get();

        $response = [
            'data' => [
                'companies' => $companies,
                'condition' => $condition,
                'model' => $model,
                'supplier' => $supplier,
                'user_received' => $userReceived,
                'location' => $location,
                'subLocation' => $subLocation,
                'employee' => $employee,
                'project' => $project,
            ]
        ];
        return response()->json($response);
    }

    public function saveAsset(Request $request)
    {
        $request->validate([
            'company' => 'required',
            'model' => 'required',
            'category' => 'required',
            'condition' => 'required',
            'serialNumber' => 'required',
            'assetName' => 'required|string|max:255',
            'purchaseDate' => 'required',
            'supplier' => 'required',
            'orderNumber' => 'required',
            'purchaseCost' => 'required|numeric',
            'warranty' => 'required|numeric',
            'receivedBy' => 'required',
            'location' => 'required',
            'quantity' => 'required|numeric',
            'notes' => 'nullable',
        ]);


        DB::beginTransaction();
        try {
            $count = 0;

            while ($count < $request->quantity) {
                $assetTypeCode = ModelAsset::select('AssetTypeCode', 'ModelCode')->where('ModelCode', $request->model)->firstOrFail();
                $alias = AssetType::select('Alias')->where('AssetTypeCode', $assetTypeCode->AssetTypeCode)->firstOrFail();

                $year = '/' . date('Y');
                $month = date('m');
                $month = (strlen($month) == 1) ? '/0' . $month : '/' . $month;

                // GENERATE COUNTER NUMBER BARU TIAP TAHUN NYA
                $assetCodeBase = $request->company . '/' . $alias->Alias . $month . $year;
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
                    'CompanyCode' => $request->company,
                    'ModelCode' => $request->model,
                    'Condition' => $request->condition,
                    'AssetBarcode' => $AssetCode,
                    'AssetCode' => $AssetCode,
                    'SerialNumber' => $request->serialNumber,
                    'AssetName' => $request->assetName,
                    'AssetCategoryCode' => $request->category,
                    'PurchaseDate' => $request->purchaseDate,
                    'SupplierCode' => $request->supplier,
                    'OrderNumber' => $request->orderNumber,
                    'PurchaseCost' => $request->purchaseCost,
                    'Warranty' => $request->warranty,
                    'Notes' => $request->notes,
                    'LocationCode' => $request->location,
                    // 'CreatedBy' => Auth::user()->id,
                    'CreatedBy' => 'yocky',
                    'ReceivedBy' => $request->receivedBy,
                    'CreatedDate' => date('Y-m-d H:i:s'),
                    'LastUpdatedBy' => '1',
                ]);
                // END SAVE ASSET

                SudahPrint::create([
                    'Barcode' => $AssetCode,
                    'CreatedDate' => date('Y-m-d H:i:s'),
                    'Status' => 0
                ]);

                // INSERT DATA KE CHECK IN
                $check_in_code = 'CKI';

                $counterCKI = Counter::select('CounterNumber')->where('CounterId', $check_in_code)->firstOrfail();
                $counterCKI->CounterNumber = $counterCKI->CounterNumber + 1;

                $check_in_code = $counterCKI->CounterNumber;
                $counterCKI->save();

                TxCheckIn::create([
                    'CheckInCode' => $check_in_code,
                    'CheckOutCode' => '',
                    'AssetCode' => $AssetCode,
                    'Condition' => $request->condition,
                    'CheckInDate' => date('Y-m-d H:i:s'),
                    'Notes' => $request->notes,
                    'Active' => 'A',
                    'Status' => true,
                    // 'CreatedBy' => Auth::user()->id,
                    'CreatedBy' => 'yocky',
                    'CreatedDate' => date('Y-m-d H:i:s'),
                    'DeliveredBy' => '',
                    'ReceivedBy' => $request->receivedBy,
                    'CheckInLocation' => $request->location
                ]);
                // END INSERT DATA KE CHECK IN

                $count++;
            }
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Asset created successfully!'
            ], 201);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function updateAsset(Request $request, $id)
    {
        $request->validate([
            'serialNumber' => 'required',
            'assetName' => 'required|string|max:255',
            'purchaseDate' => 'required',
            'supplier' => 'required',
            'orderNumber' => 'required',
            'purchaseCost' => 'required|numeric',
            'warranty' => 'required|numeric',
            'receivedBy' => 'required',
            'notes' => 'nullable',
            'status' => 'required'
        ]);

        DB::beginTransaction();
        try {
            DB::table('MsAsset')
                ->where('RowId', $id)
                ->update([
                    'Condition' => $request->condition,
                    'SerialNumber' => $request->serialNumber,
                    'AssetName' => $request->assetName,
                    'AssetCategoryCode' => $request->category,
                    'PurchaseDate' => $request->purchaseDate,
                    'SupplierCode' => $request->supplier,
                    'OrderNumber' => $request->orderNumber,
                    'Warranty' => $request->warranty,
                    'Notes' => $request->notes,
                    'ReceivedBy' => $request->receivedBy,
                    // 'LastUpdatedBy' => Auth::user()->id,
                    'LastUpdatedBy' => 'yocky',
                    'LastUpdatedDate' => date('Y-m-d H:i:s'),
                ]);

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Asset updated successfully!'
            ], 201);
        } catch (\Throwable $th) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function checkIn(Request $request)
    {
        $request->validate([
            'checkin_date' => 'required',
            'checkout_code' => 'required',
            'asset_code' => 'required',
            'condition' => 'required',
            'received_by' => 'required',
            'delivered_by' => 'required',
            'notes' => 'nullable|string|max:200',
            'checkin_location' => 'required',
        ]);

        DB::beginTransaction();

        try {
            $format = 'CKI';
            $counter = Counter::where('CounterId', $format)->firstOrFail();
            $counter->CounterNumber = $counter->CounterNumber + 1;

            $check_in_code = $format . $counter->CounterNumber;
            $counter->save();

            TxCheckIn::create([
                'CheckInCode' => $check_in_code,
                'CheckOutCode' => $request->checkout_code,
                'AssetCode' => $request->asset_code,
                'Condition' => $request->condition,
                'CheckInDate' => date($request->checkin_date),
                'Notes' => $request->notes,
                'Active' => '1',
                'Status' => 'A',
                // 'CreatedBy' => Auth::user()->id,
                'CreatedBy' => 'yocky',
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

    public function getAssetCheckout(Request $request, $id)
    {
        $asset = Asset::with('assetModel')->where('RowId', $id)->firstOrFail();
        $checkIn = TxCheckIn::where('AssetCode', $asset->AssetCode)->orderBy('CheckInDate', 'desc')->firstOrFail();
        $response = [
            'assetCode' => $asset->AssetCode,
            'assetName' => $asset->AssetName,
            'model' => $asset->assetModel->ModelName,
            'status' => $asset->Status == 'A' ? 'Available' : ($asset->Status == 'CO' ? 'Checkout' : 'SDPS'),
            'purchaseDate' => $asset->PurchaseDate,
            'lastCheckIn' => $checkIn->CheckInDate,
        ];
        return response()->json($response);
    }

    public function getAssetCheckin(Request $request, $id)
    {
        $asset = Asset::with('assetModel')->where('RowId', $id)->firstOrFail();
        $checkOut = TxCheckOut::with('projectLocation', 'createdPerson')
            ->where('AssetCode', $asset->AssetCode)
            ->orderBy('CheckOutDate', 'desc')
            ->first();

        try {
            $response = [
                'model' => $asset->assetModel->ModelName,
                'checkout_code' => $checkOut->CheckOutCode,
                'assetCode' => $asset->AssetCode,
                'assetName' => $asset->AssetName,
                'checkout_date' => $checkOut->CheckOutDate,
                'expected_checkin_date' => $checkOut->ExpectedCheckIn,
                'checkout_notes' => $checkOut->Notes,
                'checkout_project_location' => $checkOut->projectLocation->LocationCode . ' - ' . $checkOut->projectLocation->LocationName,
                'checkout_to' => $checkOut->createdPerson->NIK . ' - ' . $checkOut->createdPerson->Nama,
            ];
            return response()->json($response);
        } catch (\Throwable $th) {
            Log::debug($th->getMessage());
            return response()->json($th->getMessage(), 500);
        }
    }

    public function checkOut(Request $request)
    {
        $request->validate([
            'asset_code' => 'nullable',
            'asset_name' => 'required|string|max:255',
            'model' => 'nullable',
            'purchase_date' => 'required',
            'checkout_to' => 'required',
            'sub_location' => 'required',
            'project_location_code' => 'required',
            'project_code' => 'required',
            'checkout_date' => 'required',
            'expected_checkin' => 'required',
            'notes' => 'nullable|string',
            'checkout_by' => 'required',
            'delivered_by' => 'required',
            'acknowledge_by' => 'required',
        ]);

        DB::beginTransaction();
        try {
            $format = 'CKO';
            $counter = Counter::where('CounterId', $format)->first();

            if ($counter) {
                $counter->CounterNumber++;
                $counter->save();

                $count = $counter->CounterNumber;
            } else {
                $count = 1;
            }

            $check_out_code = $format . $count;

            TxCheckOut::create([
                'CheckOutCode' => $check_out_code,
                'AssetCode' => $request->asset_code,
                'DeliveredBy' => $request->delivered_by,
                'CheckOutTo' => $request->checkout_to,
                'CheckOutDate' => date($request->checkout_date),
                'ExpectedCheckIn' => isset($request->expected_checkin) ? date($request->expected_checkin) : '1900-01-01',
                'ProjectCode' => $request->project_code,
                'ProjectLocationCode' => $request->project_location_code,
                'SubLocation' => $request->sub_location,
                'AcknowledgeBy' => $request->acknowledge_by,
                'Notes' => $request->notes,
                'CreatedBy' => 'yocky',
                'CreatedDate' => date('Y-m-d H:i:s'),
                'LastUpdatedDate' => date('Y-m-d H:i:s'),
                'Active' => 'A',
                'Status' => '1',
                'CheckOutBy' => $request->checkout_by,
                'LastUpdatedBy' => 'yocky',
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
