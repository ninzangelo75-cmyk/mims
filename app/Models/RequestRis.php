<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequestRis extends Model
{
    use HasFactory;

    protected $table = 'request_ris';

    protected $primaryKey = 'req_ris';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'ris_no',
        'division',
        'department',
        'itemcode',
        'req_qty',
        'isavailable',
        'remarks',
        'requestedby',
        'approvedby',
        'receivedby',
        'requestedat',
        'approvedat',
        'receivedat',
    ];

    protected $casts = [
        'isavailable' => 'boolean',
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
     * Get the user who requested
     */
    public function requester()
    {
        return $this->belongsTo(User::class, 'requestedby', 'useid');
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
        return $this->hasMany(ReleasingRis::class, 'req_ris', 'req_ris');
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
