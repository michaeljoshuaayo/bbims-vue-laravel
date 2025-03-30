<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\BloodInventory;

class UpdateExpiredBlood extends Command
{
    protected $signature = 'blood:update-expired';
    protected $description = 'Update the status of expired blood items to EXPIRED';

    public function handle()
    {
        $today = now()->toDateString();

        BloodInventory::where('expiryDate', '<', $today)
            ->where('inventoryStatus', 'AVAILABLE')
            ->update(['inventoryStatus' => 'EXPIRED']);

        $this->info('Expired blood items have been updated successfully.');
    }
}
