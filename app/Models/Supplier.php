<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;
    protected $table = 'MsSupplier';
    protected $primaryKey = 'RowId';
    public $timestamps = false;

    protected $fillable = [
        'RowId',
        'SupplierCode',
        'SupplierName',
        'Phone',
        'Mobile',
        'Address',
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
}
