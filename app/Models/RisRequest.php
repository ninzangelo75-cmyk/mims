<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RisRequest extends Model
{
    protected $fillable = [
        'ris_no',
        'purpose',
        'status',
        'created_by_user_id',
        'approved_by_user_id',
        'submitted_at',
        'approved_at',
        'issued_at',
    ];

    public function lines()
    {
        return $this->hasMany(RisLine::class, 'ris_request_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by_user_id', 'useid');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by_user_id', 'useid');
    }
}
