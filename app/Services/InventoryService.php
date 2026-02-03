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

    /**
     * Record multiple stock-in transactions in a single batch
     *
     * @param array<int, array<string, mixed>> $items
     * @return array<int, Receiving>
     */
    public function recordStockInMany(
        array $items,
        ?string $supplier = null,
        ?string $referenceno = null,
        ?string $department = null
    ): array {
        return DB::transaction(function () use ($items, $supplier, $referenceno, $department) {
            $receivings = [];

            foreach ($items as $item) {
                $receivings[] = $this->recordStockIn(
                    (int) $item['itemcode'],
                    (float) $item['qty'],
                    (string) $item['batchno'],
                    (string) $item['expirydate'],
                    (string) $item['uom'],
                    (float) $item['unitprice'],
                    $supplier,
                    $referenceno,
                    $department
                );
            }

            return $receivings;
        });
    }
}

