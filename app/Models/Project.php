<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;
    protected $table = 'MsProject';
    protected $primaryKey = 'RowId';
    public $timestamps = false;

    protected $fillable = [
        'RowId',
        'ProjectCode',
        'ProjectName',
        'Status',
        'Active',
        'CreatedDate',
        'CreatedBy',
        'LastUpdatedBy'
    ];

    public static function getNextRowId()
    {
        return parent::max('RowId') + 1;
    }
}
