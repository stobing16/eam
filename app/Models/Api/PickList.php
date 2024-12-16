<?php

namespace App\Models\Api;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PickList extends Model
{
    use HasFactory;
    protected $table = 'MsPickList';
    protected $primaryKey = 'RowId';
    public $timestamps = false;
}
