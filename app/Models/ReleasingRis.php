<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReleasingRis extends Model
{
    use HasFactory;

    protected $table = 'releasing_ris';

    protected $primaryKey = 'rel_ris_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'req_ris',
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
        return $this->belongsTo(RequestRis::class, 'req_ris', 'req_ris');
    }

    /**
     * Get the user who released
     */
    public function releaser()
    {
        return $this->belongsTo(User::class, 'releaseby', 'useid');
    }
}
