<?php

namespace OnrampLab\AuditingLog\Concerns;

use Illuminate\Support\Collection;
use OnrampLab\AuditingLog\ValueObjects\Actor;
use ReflectionClass;
use Spatie\Activitylog\Facades\LogBatch;
use Spatie\Activitylog\Models\Activity;

trait auditable
{
    public Actor $auditActor;

    public ?string $auditBatchUuid = null;

    public ?string $auditResourceClass = null;

    public ?Collection $auditResourceIds = null;

    public ?string $auditEvent = null;

    public ?string $auditLogName = null;

    public string $auditDescription;

    public function logAuditing(): void
    {
        if ($this->auditResourceIds) {
            $this->auditResourceIds->each(function (int|string $resourceId) {
                $this->logWithBatch($resourceId);
            });
        } else {
            $this->logWithBatch();
        }
    }

    private function logWithBatch(int|string|null $resourceId = null): void
    {
        LogBatch::startBatch();
        if ($this->auditBatchUuid) {
            LogBatch::setBatch($this->auditBatchUuid);
        }

        activity()
            ->withProperties($this->auditActor->properties)
            ->tap(function (Activity $activity) use ($resourceId) {
                $activity->fill($this->getLogAttributes());
                $activity->subject_id = $resourceId;
            })
            ->log($this->auditDescription);

        LogBatch::endBatch();
    }

    private function getEvent(): string
    {
        $reflect = new ReflectionClass($this);
        $name = $reflect->getShortName();
        /** @var string $displayName */
        $displayName = preg_replace('/(.)([A-Z])/', '$1_$2', $name);
        return strtolower($displayName);
    }

    private function getLogAttributes(): array
    {
        $this->auditEvent = $this->auditEvent ?: $this->getEvent();
        return [
            'log_name' => $this->auditLogName ?: config('activitylog.default_log_name'),
            'subject_type' => $this->auditResourceClass,
            'event' => $this->auditEvent,
            'causer_type' => $this->auditActor->actorClass,
            'causer_id' => $this->auditActor->actorId
        ];
    }
}
