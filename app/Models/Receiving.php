<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Receiving extends Model
{
    use HasFactory;

    protected $table = 'receiving';

    protected $primaryKey = 'recid';
    public $incrementing = true;
    protected $keyType = 'bigint';

    protected $fillable = [
        'itemcode',
        'datereceived',
        'supplier',
        'referenceno',
        'qty',
        'uom',
        'unitprice',
        'totalamount',
        'batchno',
        'expirydate',
        'department',
    ];

    protected $casts = [
        'datereceived' => 'datetime',
        'expirydate' => 'date',
        'qty' => 'decimal:2',
        'unitprice' => 'decimal:2',
        'totalamount' => 'decimal:2',
    ];

    /**
     * Get the item that owns this receiving record
     */
    public function item()
    {
        return $this->belongsTo(Item::class, 'itemcode', 'itemcode');
    }

    /**
     * Scope to filter expired batches
     */
    public function scopeExpired($query)
    {
        return $query->where('expirydate', '<', now());
    }

    /**
     * Scope to filter near-expiry batches (within 30 days)
     */
    public function scopeNearExpiry($query, $days = 30)
    {
        return $query->whereBetween('expirydate', [now(), now()->addDays($days)]);
    }

    /**
     * Scope to filter safe batches
     */
    public function scopeSafe($query, $days = 30)
    {
        return $query->where('expirydate', '>', now()->addDays($days));
    }

    /**
     * Scope for FEFO ordering
     */
    public function scopeFefo($query)
    {
        return $query->orderBy('expirydate', 'asc');
    }
}
