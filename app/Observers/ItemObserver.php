<?php

namespace App\Observers;

use App\Models\Item;
use App\Services\AuditLogService;

class ItemObserver
{
    protected $auditLogService;

    public function __construct(AuditLogService $auditLogService)
    {
        $this->auditLogService = $auditLogService;
    }

    public function created(Item $item)
    {
        $this->auditLogService->log('CREATE', $item, null, $item->toArray());
    }

    public function updated(Item $item)
    {
        $this->auditLogService->log('UPDATE', $item, $item->getOriginal(), $item->getChanges());
    }

    public function deleted(Item $item)
    {
        $this->auditLogService->log('DELETE', $item, $item->toArray(), null);
    }
}
