<?php

namespace App\Console\Commands;

use App\Models\Receiving;
use Illuminate\Console\Command;

class CheckExpiry extends Command
{
    protected $signature = 'inventory:check-expiry';
    protected $description = 'Check for expired batches and mark them';

    public function handle()
    {
        $expiredBatches = Receiving::expired()->count();
        
        $this->info("Found {$expiredBatches} expired batch(es).");
        
        // In a real implementation, you might want to send notifications here
        // or mark batches as expired in a separate flag column
        
        return Command::SUCCESS;
    }
}
