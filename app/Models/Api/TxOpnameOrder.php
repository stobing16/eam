<?php

namespace App\Models\Api;

use App\Models\Location;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TxOpnameOrder extends Model
{
    use HasFactory;
    protected $table = 'TxOpnameOrder';
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
        'LastUpdatedBy',
        'LastUpdatedDate',
    ];

    public static function getNextRowId()
    {
        return parent::max('RowId') + 1;
    }

    public function location()
    {
        return $this->belongsTo(Location::class, 'LocationCode', 'LocationCode');
    }

    public function opnameAndroids()
    {
        return $this->hasMany(TxOpnameAssetAndroidSingleTemp::class, 'OpnameOrderId', 'OpnameOrderId')->select('OpnameOrderId');
    }
}
