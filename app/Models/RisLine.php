<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RisLine extends Model
{
    protected $fillable = [
        'ris_request_id',
        'consumable_item_id',
        'qty_requested',
        'qty_issued',
    ];

    public function ris()
    {
        return $this->belongsTo(RisRequest::class, 'ris_request_id');
    }

    public function item()
    {
        return $this->belongsTo(ConsumableItem::class, 'consumable_item_id');
    }
}
