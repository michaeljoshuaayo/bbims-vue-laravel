<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequisitionItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'blood_request_id',
        'blood_component',
        'blood_type',
        'quantity',
        'remarks',
    ];

    public function bloodRequest()
    {
        return $this->belongsTo(BloodRequest::class);
    }
}