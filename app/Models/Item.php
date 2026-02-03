<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Item extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'itemcode';
    public $incrementing = true;
    protected $keyType = 'bigint';

    protected $fillable = [
        'itemname',
        'description',
        'brand',
        'dosage_form',
        'strength',
        'default_uom',
    ];

    /**
     * Get all receiving records for this item
     */
    public function receivings()
    {
        return $this->hasMany(Receiving::class, 'itemcode', 'itemcode');
    }

    /**
     * Get all RIS requests for this item
     */
    public function requestRis()
    {
        return $this->hasMany(RequestRis::class, 'itemcode', 'itemcode');
    }

    /**
     * Get all PTR requests for this item
     */
    public function requestPtr()
    {
        return $this->hasMany(RequestPtr::class, 'itemcode', 'itemcode');
    }

    /**
     * Scope to calculate inventory
     */
    public function scopeWithInventory($query)
    {
        // Use subqueries instead of GROUP BY items.* (MariaDB ONLY_FULL_GROUP_BY safe)
        $stockIn = DB::table('receiving')
            ->selectRaw('itemcode, SUM(qty) as stockin')
            ->groupBy('itemcode');

        $stockOutRis = DB::table('releasing_ris')
            ->join('request_ris', 'releasing_ris.req_ris', '=', 'request_ris.req_ris')
            ->selectRaw('request_ris.itemcode as itemcode, SUM(releasing_ris.release_qty) as stockout_ris')
            ->groupBy('request_ris.itemcode');

        $stockOutPtr = DB::table('releasing_ptr')
            ->join('request_ptr', 'releasing_ptr.ptr_no', '=', 'request_ptr.ptr_no')
            ->selectRaw('request_ptr.itemcode as itemcode, SUM(releasing_ptr.release_qty) as stockout_ptr')
            ->groupBy('request_ptr.itemcode');

        return $query
            ->select('items.*')
            ->leftJoinSub($stockIn, 'stock_in', function ($join) {
                $join->on('stock_in.itemcode', '=', 'items.itemcode');
            })
            ->leftJoinSub($stockOutRis, 'stock_out_ris', function ($join) {
                $join->on('stock_out_ris.itemcode', '=', 'items.itemcode');
            })
            ->leftJoinSub($stockOutPtr, 'stock_out_ptr', function ($join) {
                $join->on('stock_out_ptr.itemcode', '=', 'items.itemcode');
            })
            ->addSelect([
                DB::raw('IFNULL(stock_in.stockin, 0) AS stockin'),
                DB::raw('(IFNULL(stock_out_ris.stockout_ris, 0) + IFNULL(stock_out_ptr.stockout_ptr, 0)) AS stockout'),
                DB::raw('(IFNULL(stock_in.stockin, 0) - (IFNULL(stock_out_ris.stockout_ris, 0) + IFNULL(stock_out_ptr.stockout_ptr, 0))) AS remaining'),
            ]);
    }

    /**
     * Scope for department filtering
     */
    public function scopeForDepartment($query, $department)
    {
        return $query->whereHas('receivings', function ($q) use ($department) {
            $q->where('department', $department);
        });
    }
}
