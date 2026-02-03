<?php

namespace App\Services;

use App\Models\Receiving;
use App\Models\Item;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InventoryService
{
    /**
     * Record stock-in transaction
     */
    public function recordStockIn(
        int $itemcode,
        float $qty,
        string $batchno,
        string $expirydate,
        string $uom,
        float $unitprice,
        ?string $supplier = null,
        ?string $referenceno = null,
        ?string $department = null
    ): Receiving {
        return DB::transaction(function () use (
            $itemcode,
            $qty,
            $batchno,
            $expirydate,
            $uom,
            $unitprice,
            $supplier,
            $referenceno,
            $department
        ) {
            $totalamount = $qty * $unitprice;

            $receiving = Receiving::create([
                'itemcode' => $itemcode,
                'datereceived' => now(),
                'supplier' => $supplier,
                'referenceno' => $referenceno,
                'qty' => $qty,
                'uom' => $uom,
                'unitprice' => $unitprice,
                'totalamount' => $totalamount,
                'batchno' => $batchno,
                'expirydate' => $expirydate,
                'department' => $department,
            ]);

            // Invalidate inventory cache
            cache()->forget("inventory_item_{$itemcode}");

            return $receiving;
        });
    }
}

