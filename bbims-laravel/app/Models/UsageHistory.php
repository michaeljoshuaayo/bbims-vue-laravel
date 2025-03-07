<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UsageHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'blood_request_id',
        'blood_serial_number',
        'blood_type',
        'blood_component',
        'remarks',
    ];

    public function bloodRequest()
    {
        return $this->belongsTo(BloodRequest::class);
    }
}
