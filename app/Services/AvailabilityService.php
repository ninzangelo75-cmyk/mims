<?php

namespace App\Services;

use App\Models\Item;
use Illuminate\Support\Facades\DB;

class AvailabilityService
{
    /**
     * Check availability of an item
     */
    public function checkAvailability(int $itemcode, float $reqQty): array
    {
        $item = Item::withInventory()
            ->where('items.itemcode', $itemcode)
            ->first();

        if (!$item) {
            return [
                'available' => false,
                'available_qty' => 0,
                'message' => 'Item not found',
            ];
        }

        $remaining = (float) $item->remaining;

        // Check for expired batches
        $expiredBatches = DB::table('receiving')
            ->where('itemcode', $itemcode)
            ->where('expirydate', '<', now())
            ->sum('qty');

        $availableQty = max(0, $remaining - $expiredBatches);

        return [
            'available' => $availableQty >= $reqQty,
            'available_qty' => $availableQty,
            'remaining' => $remaining,
            'message' => $availableQty >= $reqQty 
                ? 'Available' 
                : "Only {$availableQty} units available",
        ];
    }
}

