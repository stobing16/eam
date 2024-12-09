<?php

namespace App\Models\Api;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssetType extends Model
{
    use HasFactory;
    protected $table = 'MsAssetType';
    protected $primaryKey = 'RowId';
    public $timestamps = false;

    protected $fillable = [
        'MainGroupCode',
        'AssetTypeCode',
        'AssetType',
        'Alias',
        'Status',
        'Active',
        'CreatedBy',
        'CreatedDate',
    ];

    public static function getNextRowId()
    {
        return parent::max('RowId') + 1;
    }
}
