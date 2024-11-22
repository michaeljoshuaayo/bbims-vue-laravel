<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BloodInventory extends Model
{
    use HasFactory;
    protected $table = 'blood_inventories' ;
    protected $fillable = [
        'bloodSerialNumber',
        'bloodType',
        'bloodComponent',
        'expiryDate',
        'inventoryStatus',
    ];
}
