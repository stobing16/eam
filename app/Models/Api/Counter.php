<?php

namespace App\Models\Api;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Counter extends Model
{
    use HasFactory;
    protected $table = 'MsCounter';
    protected $primaryKey = 'RowId';
    public $timestamps = false;

    protected $fillable = [
        'CounterId',
        'CounterNumber',
        'CounterFormat',
        'Description',
        'Other',
    ];

    public static function getNextRowId()
    {
        return parent::max('RowId') + 1;
    }
}
