<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PtrRequest extends Model
{
    protected $fillable = [
        'ptr_no',
        'status',
        'asset_id',
        'from_user_id',
        'to_user_id',
        'reason',
        'created_by_user_id',
        'verified_by_user_id',
        'approved_by_user_id',
        'submitted_at',
        'verified_at',
        'approved_at',
        'completed_at',
    ];

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }

    public function fromUser()
    {
        return $this->belongsTo(User::class, 'from_user_id', 'useid');
    }

    public function toUser()
    {
        return $this->belongsTo(User::class, 'to_user_id', 'useid');
    }
}
