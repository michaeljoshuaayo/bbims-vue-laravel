<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BloodRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'requesting_facility',
        'address',
        'pathologist',
        'facility_transac_num',
        'requested_by',
        'uncrossmatched_whole_blood_a_plus',
        'uncrossmatched_whole_blood_a_minus',
        'uncrossmatched_whole_blood_b_plus',
        'uncrossmatched_whole_blood_b_minus',
        'uncrossmatched_whole_blood_o_plus',
        'uncrossmatched_whole_blood_o_minus',
        'uncrossmatched_whole_blood_ab_plus',
        'uncrossmatched_whole_blood_ab_minus',
        'uncrossmatched_packed_rbc_a_plus',
        'uncrossmatched_packed_rbc_a_minus',
        'uncrossmatched_packed_rbc_b_plus',
        'uncrossmatched_packed_rbc_b_minus',
        'uncrossmatched_packed_rbc_o_plus',
        'uncrossmatched_packed_rbc_o_minus',
        'uncrossmatched_packed_rbc_ab_plus',
        'uncrossmatched_packed_rbc_ab_minus',
        'crossmatched_whole_blood_a_plus',
        'crossmatched_whole_blood_a_minus',
        'crossmatched_whole_blood_b_plus',
        'crossmatched_whole_blood_b_minus',
        'crossmatched_whole_blood_o_plus',
        'crossmatched_whole_blood_o_minus',
        'crossmatched_whole_blood_ab_plus',
        'crossmatched_whole_blood_ab_minus',
        'crossmatched_packed_rbc_a_plus',
        'crossmatched_packed_rbc_a_minus',
        'crossmatched_packed_rbc_b_plus',
        'crossmatched_packed_rbc_b_minus',
        'crossmatched_packed_rbc_o_plus',
        'crossmatched_packed_rbc_o_minus',
        'crossmatched_packed_rbc_ab_plus',
        'crossmatched_packed_rbc_ab_minus',
    ];

    public function requisitionItems()
    {
        return $this->hasMany(RequisitionItem::class);
    }
}