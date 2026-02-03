<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequestPtr extends Model
{
    use HasFactory;

    protected $table = 'request_ptr';

    protected $primaryKey = 'req_ptr';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'ptr_no',
        'division',
        'target',
        'trans_type',
        'trans_type_other',
        'itemcode',
        'req_qty',
        'remarks',
        'purpose',
        'approvedby',
        'receivedby',
        'requestedat',
        'approvedat',
        'receivedat',
    ];

    protected $casts = [
        'req_qty' => 'decimal:2',
        'requestedat' => 'datetime',
        'approvedat' => 'datetime',
        'receivedat' => 'datetime',
    ];

    /**
     * Get the item for this request
     */
    public function item()
    {
        return $this->belongsTo(Item::class, 'itemcode', 'itemcode');
    }

    /**
     * Get the user who approved
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approvedby', 'useid');
    }

    /**
     * Get the user who received
     */
    public function receiver()
    {
        return $this->belongsTo(User::class, 'receivedby', 'useid');
    }

    /**
     * Get all releases for this request
     */
    public function releases()
    {
        return $this->hasMany(ReleasingPtr::class, 'ptr_no', 'ptr_no');
    }

    /**
     * Get total released quantity
     */
    public function getTotalReleasedAttribute()
    {
        return $this->releases()->sum('release_qty');
    }

    /**
     * Get remaining quantity to release
     */
    public function getRemainingQtyAttribute()
    {
        return $this->req_qty - $this->total_released;
    }

    /**
     * Check if request is fully released
     */
    public function isFullyReleased(): bool
    {
        return $this->total_released >= $this->req_qty;
    }

    /**
     * Check if request is pending
     */
    public function isPending(): bool
    {
        return is_null($this->approvedat);
    }

    /**
     * Check if request is approved
     */
    public function isApproved(): bool
    {
        return !is_null($this->approvedat);
    }
}
