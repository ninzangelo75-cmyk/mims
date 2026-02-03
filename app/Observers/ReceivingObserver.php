<?php

namespace App\Observers;

use App\Models\Receiving;
use App\Services\AuditLogService;

class ReceivingObserver
{
    protected $auditLogService;

    public function __construct(AuditLogService $auditLogService)
    {
        $this->auditLogService = $auditLogService;
    }

    public function created(Receiving $receiving)
    {
        $this->auditLogService->log('CREATE', $receiving, null, $receiving->toArray());
    }

    public function updated(Receiving $receiving)
    {
        $this->auditLogService->log('UPDATE', $receiving, $receiving->getOriginal(), $receiving->getChanges());
    }
}
