<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubLocation extends Model
{
    use HasFactory;
    protected $table = 'MsSubLocation';
    protected $primaryKey = 'RowId';
    public $timestamps = false;

    protected $fillable = [
        'RowId',
        'SubLocationCode',
        'SubLocationName',
        'Status',
        'Active',
        'CreatedDate',
        'CreatedBy'
    ];

    public static function getNextRowId()
    {
        return parent::max('RowId') + 1;
    }
}
