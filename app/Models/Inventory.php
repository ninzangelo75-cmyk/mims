<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    protected $primaryKey = 'consumable_item_id';
    public $incrementing = false;
    protected $keyType = 'int';

    protected $fillable = ['consumable_item_id', 'qty_on_hand'];

    public function item()
    {
        return $this->belongsTo(ConsumableItem::class, 'consumable_item_id');
    }
}
