<?php

namespace App\Models\Api;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TxCheckIn extends Model
{
    use HasFactory;
    protected $table = 'TxCheckIn';
    protected $primaryKey = 'RowId';
    public $timestamps = false;

    protected $fillable = [
        'RowId',
        'CheckInCode',
        'CheckOutCode',
        'AssetCode',
        'Condition',
        'CheckInDate',
        'CheckInLocation',
        'Notes',
        'Status',
        'Active',
        'DeliveredBy',
        'ReceivedBy',
        'CreatedDate',
        'CreatedBy',
    ];

    public static function getNextRowId()
    {
        return parent::max('RowId') + 1;
    }
}
