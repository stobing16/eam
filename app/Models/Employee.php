<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;
    protected $table = 'MsEmployees';
    protected $primaryKey = 'RowId';
    public $timestamps = false;

    protected $fillable = [
        'RowId',
        'NIK',
        'Nama',
        'Email',
        'Jabatan',
        'Status',
        'Active',
        'CreatedDate'
    ];

    public static function getNextRowId()
    {
        return Employee::max('RowId') + 1;
    }
}
