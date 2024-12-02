<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;
    protected $connection = 'sqlsrv';
    protected $table = 'MsEmployees';
    public $timestamps = false;
    protected $fillable = [
        'RowId', 'Nama', 'Email','CreatedDate'
    ];

    public static function getNextRowId()
    {
        return Employee::max('RowId') + 1;
    }
}
