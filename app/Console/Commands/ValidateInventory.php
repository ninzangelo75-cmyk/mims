<?php

namespace App\Console\Commands;

use App\Models\Item;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ValidateInventory extends Command
{
    protected $signature = 'inventory:validate';
    protected $description = 'Validate inventory integrity';

    public function handle()
    {
        $this->info('Validating inventory integrity...');

        // Check for negative stock
        $items = Item::withInventory()->get();
        $negativeStock = $items->filter(fn($item) => $item->remaining < 0);

        if ($negativeStock->count() > 0) {
            $this->error("Found {$negativeStock->count()} item(s) with negative stock:");
            foreach ($negativeStock as $item) {
                $this->line("  - {$item->itemname}: {$item->remaining}");
            }
        } else {
            $this->info('No negative stock found.');
        }

        // Check foreign key integrity
        $orphanedReceivings = DB::table('receiving')
            ->leftJoin('items', 'receiving.itemcode', '=', 'items.itemcode')
            ->whereNull('items.itemcode')
            ->count();

        if ($orphanedReceivings > 0) {
            $this->error("Found {$orphanedReceivings} orphaned receiving record(s).");
        } else {
            $this->info('Foreign key integrity OK.');
        }

        return Command::SUCCESS;
    }
}
