<?php

namespace App\Models\Api;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TxCheckOut extends Model
{
    use HasFactory;
    protected $table = 'TxCheckOut';
    protected $primaryKey = 'RowId';
    public $timestamps = false;

    protected $fillable = [
        'RowId',
        'CheckOutCode',
        'AssetCode',
        'DeliveredBy',
        'CheckOutTo',
        'CheckOutDate',
        'ExpectedCheckIn',
        'ProjectCode',
        'ProjectLocationCode',
        'SubLocation',
        'AcknowledgeBy',
        'Notes',
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
