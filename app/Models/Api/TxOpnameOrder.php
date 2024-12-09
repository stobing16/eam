<?php

namespace App\Models\Api;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TxOpnameOrder extends Model
{
    use HasFactory;
    protected $table = 'TxCheckOut';
    protected $primaryKey = 'RowId';
    public $timestamps = false;

    protected $fillable = [
        'RowId',
        'OpnameOrderId',
        'OpnameOrderDate',
        'OpnameOrderType',
        'LocationCode',
        'ClosedOn',
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
