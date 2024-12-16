<?php

namespace App\Models\Api;

use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        'OrderNumber',
        'PurchaseCost',
        'Warranty',
        'Notes',
        'LocationCode',
        'ReceivedBy',
        'Status',
        'Active',
        'CreatedDate',
        'CreatedBy'
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
        return $this->belongsTo(ModelAsset::class, 'SupplierCode', 'SupplierCode');
    }

    public function location()
    {
        return $this->belongsTo(ModelAsset::class, 'LocationCode', 'LocationCode');
    }
}
