<?php

namespace App\Models\Api;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    use HasFactory;
    protected $table = 'MsBrand';
    protected $primaryKey = 'RowId';
    public $timestamps = false;

    protected $fillable = [
        'MainGroupCode',
        'AssetTypeCode',
        'BrandName',
        'BrandCode',
        'Status',
        'Active',
        'CreatedBy',
        'CreatedDate',
        'LastUpdatedBy'
    ];

    public static function getNextRowId()
    {
        return parent::max('RowId') + 1;
    }
}
