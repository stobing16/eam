<?php

namespace App\Models\Api;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SudahPrint extends Model
{
    use HasFactory;
    protected $table = 'SudahPrint';
    protected $primaryKey = 'RowId';
    public $timestamps = false;

    protected $fillable = [
        'Barcode',
        'CreatedDate',
        'Status',
    ];

    public static function getNextRowId()
    {
        return parent::max('RowId') + 1;
    }
}
