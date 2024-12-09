<?php

namespace App\Models\Api;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModelAsset extends Model
{
    use HasFactory;
    protected $table = 'MsModel';
    protected $primaryKey = 'RowId';
    public $timestamps = false;

    protected $fillable = [
        'RowId',
        'MainGroupCode',
        'AssetTypeCode',
        'BrandCode',
        'ModelCode',
        'ModelName',
        'Status',
        'Active',
        'CreatedDate',
        'CreatedBy',
    ];

    public static function getNextRowId()
    {
        return parent::max('RowId') + 1;
    }
}
