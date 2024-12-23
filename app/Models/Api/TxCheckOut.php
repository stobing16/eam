<?php

namespace App\Models\Api;

use App\Models\Employee;
use App\Models\Location;
use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

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
        'CheckOutBy',
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
        'LastUpdatedDate',
        'LastUpdatedBy',
    ];

    public static function getNextRowId()
    {
        return parent::max('RowId') + 1;
    }

    public function asset()
    {
        return $this->belongsTo(Asset::class, 'AssetCode', 'AssetCode');
    }

    public function createdPerson()
    {
        return $this->belongsTo(Employee::class, 'CheckOutTo', 'NIK');
    }

    public function txCheckIn()
    {
        return $this->belongsTo(TxCheckIn::class, 'CheckOutCode', 'CheckOutCode');
    }

    public function projectLocation()
    {
        return $this->belongsTo(Location::class, 'ProjectLocationCode', 'LocationCode');
    }
}
