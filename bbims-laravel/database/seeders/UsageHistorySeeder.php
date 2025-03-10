<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\UsageHistory;
use App\Models\BloodRequest;
use Carbon\Carbon;

class UsageHistorySeeder extends Seeder
{
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        UsageHistory::truncate();

        $bloodComponents = ['Whole blood', 'Packed RBC', 'Fresh Frozen Plasma', 'Platelet Concentrate'];
        $bloodTypes = ['A+', 'A-', 'B+', 'B-', 'O+', 'O-', 'AB+', 'AB-'];
        $startDate = Carbon::now()->subYears(2);
        $endDate = Carbon::now();

        // Get existing blood request IDs
        $bloodRequestIds = BloodRequest::pluck('id')->toArray();

        $data = [];

        while ($startDate <= $endDate) {
            // Generate a random number of records per day (between 1 and 30)
            $dailyEntries = rand(1, 30);

            for ($i = 0; $i < $dailyEntries; $i++) {
                $data[] = [
                    'blood_request_id' => count($bloodRequestIds) ? $bloodRequestIds[array_rand($bloodRequestIds)] : 1, 
                    'blood_serial_number' => 'BSN' . rand(1000, 9999),
                    'blood_type' => $bloodTypes[array_rand($bloodTypes)],
                    'blood_component' => $bloodComponents[array_rand($bloodComponents)],
                    'remarks' => rand(0, 1) ? 'Used for transfusion' : null,
                    'created_at' => $startDate->copy(),
                    'updated_at' => $startDate->copy(),
                ];
            }

            // Insert data in chunks to avoid too many placeholders error
            if (count($data) > 500) {
                UsageHistory::insert($data);
                $data = []; // Reset array after insert
            }

            $startDate->addDay();
        }

        // Insert remaining data
        if (!empty($data)) {
            UsageHistory::insert($data);
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
