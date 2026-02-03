<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReleasingPtr extends Model
{
    use HasFactory;

    protected $table = 'releasing_ptr';

    protected $primaryKey = 'rel_ptr_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'ptr_no',
        'release_qty',
        'releaseby',
        'releaseat',
    ];

    protected $casts = [
        'release_qty' => 'decimal:2',
        'releaseat' => 'datetime',
    ];

    /**
     * Get the request this release belongs to
     */
    public function request()
    {
        return $this->belongsTo(RequestPtr::class, 'ptr_no', 'ptr_no');
    }

    /**
     * Get the user who released
     */
    public function releaser()
    {
        return $this->belongsTo(User::class, 'releaseby', 'useid');
    }
}
