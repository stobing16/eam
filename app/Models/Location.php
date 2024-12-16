<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use HasFactory;
    protected $table = 'MsLocation';
    protected $primaryKey = 'RowId';
    public $timestamps = false;

    protected $fillable = [
        'RowId',
        'LocationCode',
        'LocationName',
        'IsDefault',
        'IsLocation',
        'IsProjectLocation',
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
