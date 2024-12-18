<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BarcodeCollectingController extends Controller
{
    public function index(Request $request)
    {
        $perPage = (int) $request->input('per_page', 10);
        $currentPage = (int) $request->input('current_page', 1);
        $search = $request->input('search', '');

        $offset = ($currentPage - 1) * $perPage;

        $baseQuery = "
            ;WITH CKOFirst AS (
                SELECT 
                    CK.CheckOutCode,
                    CK.AssetCode,
                    CK.CheckOutDate,
                    CK.CheckOutTo,
                    CK.ProjectLocationCode AS 'Location',
                    CK.SubLocation,
                    CK.ProjectCode,
                    CK.AcknowledgeBy,
                    ROW_NUMBER() OVER (PARTITION BY CK.AssetCode ORDER BY CK.RowId DESC, CK.CheckOutDate DESC) AS rn
                FROM TxCheckOut CK WITH (NOLOCK)
                WHERE CK.Status = '1'
            )
            SELECT 
                RCKO.RowId AS 'TrxId',
                AST.ModelCode,
                MD.ModelName,
                MD.BrandCode,
                BR.BrandName,
                AST.Condition,
                PlKondisi.PlDescription AS 'ConditionName',
                AST.AssetCode,
                AST.AssetName,
                ISNULL(CKO.ProjectCode, '') AS 'ProjectCode',
                ISNULL(AST.LocationCode, '-1') AS 'LocationCode',
                ISNULL(LC.LocationName, '') AS 'LocationName',
                ISNULL(CKO.SubLocation, '') AS 'SubLocationCode',
                ISNULL(SLC.SubLocationName, '') AS 'SubLocationName',
                RCKO.Notes,
                ISNULL(CRTBY.Nama, '') AS 'RequestBy',
                CONVERT(VARCHAR, RCKO.CreatedDate, 120) AS 'CreatedDate',
                CASE 
                    WHEN AST.Status = 'CO' THEN 'CheckOut'
                    WHEN AST.Status = 'A' THEN 'CheckIn'
                    ELSE 'Unknown'
                END AS 'Status',
                ISNULL(ACKBY.Nama, ISNULL(CKO.AcknowledgeBy, '')) AS 'AcknowledgeBy'
            FROM MsAsset AST
                INNER JOIN TxRequestToCheckOut RCKO WITH (NOLOCK) ON RCKO.Barcode = AST.AssetCode
                INNER JOIN MsCompany CP WITH (NOLOCK) ON CP.CompanyId = AST.CompanyCode
                INNER JOIN MsModel MD WITH (NOLOCK) ON MD.ModelCode = AST.ModelCode
                INNER JOIN MsBrand BR WITH (NOLOCK) ON BR.BrandCode = MD.BrandCode
                INNER JOIN MsPickList PlKondisi WITH (NOLOCK) ON PlKondisi.ParentId = '2' AND PlKondisi.ChildId = AST.Condition
                LEFT JOIN CKOFirst CKO ON CKO.AssetCode = AST.AssetCode
                LEFT JOIN MsSupplier SP WITH (NOLOCK) ON SP.SupplierCode = AST.SupplierCode
                LEFT JOIN MsLocation LC WITH (NOLOCK) ON LC.LocationCode = AST.LocationCode
                LEFT JOIN MsSubLocation SLC WITH (NOLOCK) ON SLC.SubLocationCode = CKO.SubLocation
                LEFT JOIN MsUser USR WITH (NOLOCK) ON USR.UserCode = RCKO.CreatedBy
                LEFT JOIN MsEmployees CRTBY WITH (NOLOCK) ON CRTBY.NIK = USR.Ssn
                LEFT JOIN MsEmployees ACKBY WITH (NOLOCK) ON ACKBY.NIK = CKO.AcknowledgeBy
            WHERE RCKO.Status = 'A'
        ";

        // Apply search filter
        if (!empty($search)) {
            $baseQuery .= " AND (AST.AssetName LIKE :search OR AST.AssetCode LIKE :search)";
        }

        // Pagination
        $paginatedQuery = "
            SELECT * 
            FROM ($baseQuery) AS paginated_results
            ORDER BY CreatedDate DESC
            OFFSET :offset ROWS FETCH NEXT :perPage ROWS ONLY
        ";

        $totalQuery = "SELECT COUNT(*) AS totalItems FROM ($baseQuery) AS total_results";

        $bindings = [
            'offset' => $offset,
            'perPage' => $perPage,
        ];

        if (!empty($search)) {
            $bindings['search'] = "%$search%";
        }

        // Execute the queries
        $data = DB::select($paginatedQuery, $bindings);
        $totalItems = DB::selectOne($totalQuery, $bindings)->totalItems;

        $totalPages = ceil($totalItems / $perPage);

        return response()->json([
            'currentPage' => $currentPage,
            'rowsPerPage' => $perPage,
            'totalPages' => $totalPages,
            'data' => $data,
        ]);
    }
}
