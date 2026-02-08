<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    protected $fillable = [
        'asset_tag',
        'description',
        'serial_no',
        'condition',
        'custodian_user_id',
    ];

    public function custodian()
    {
        return $this->belongsTo(User::class, 'custodian_user_id', 'useid');
    }
}
