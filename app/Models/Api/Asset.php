<?php

namespace App\Models\Api;

use App\Models\Company;
use App\Models\Location;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Asset extends Model
{
    use HasFactory;
    protected $table = 'MsAsset';
    protected $primaryKey = 'RowId';
    public $timestamps = false;

    protected $fillable = [
        'RowId',
        'CompanyCode',
        'ModelCode',
        'Condition',
        'AssetBarcode',
        'AssetCode',
        'AssetName',
        'AssetCategoryCode',
        'PurchaseDate',
        'SupplierCode',
        'SerialNumber',
        'OrderNumber',
        'PurchaseCost',
        'Warranty',
        'Notes',
        'LocationCode',
        'ReceivedBy',
        'Status',
        'Active',
        'CreatedDate',
        'CreatedBy',
        'LastUpdatedBy',
    ];

    public static function getNextRowId()
    {
        return parent::max('RowId') + 1;
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'CompanyCode', 'CompanyId');
    }

    public function assetModel()
    {
        return $this->belongsTo(ModelAsset::class, 'ModelCode', 'ModelCode');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'SupplierCode', 'SupplierCode');
    }

    public function location()
    {
        return $this->belongsTo(Location::class, 'LocationCode', 'LocationCode');
    }

    public function assetStatus()
    {
        return $this->hasOne(PickList::class, 'ChildId', 'Status')->where('ParentId', 3);
    }

    public function assetCondition()
    {
        return $this->hasOne(PickList::class, 'ChildId', 'Condition')->where('ParentId', 2);
    }

    public function txCheckout()
    {
        return $this->hasOne(TxCheckOut::class, 'AssetCode', 'AssetCode')
            ->orderBy('CheckOutDate', 'desc')->orderBy('RowId', 'desc');
    }
}
