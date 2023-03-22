<?php

namespace OnrampLab\AuditingLog\Tests\Unit\Concerns;

use Illuminate\Support\Str;
use OnrampLab\AuditingLog\Concerns\auditable;
use OnrampLab\AuditingLog\Tests\Classes\User;
use OnrampLab\AuditingLog\Tests\TestCase;
use OnrampLab\AuditingLog\ValueObjects\Actor;
use Spatie\Activitylog\Models\Activity;

class AuditableTest extends TestCase
{
    use Auditable;

    public function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory(2)->create();
        $this->auditActor = new Actor([
            'actorId' => 1,
            'actorClass' => 'App\Model\User',
            'properties' => [
                'ip' => '127.0.0.1'
            ]
        ]);
        $this->auditResourceIds = collect([$this->user[0]->id, $this->user[1]->id]);
        $this->auditDescription = 'test logger';
        $this->auditBatchUuid = Str::uuid();
        $this->auditResourceClass = User::class;
    }

    /**
     * @test
     */
    public function should_log_auditing_info()
    {
        $this->logAuditing();
        $activity = Activity::forBatch($this->auditBatchUuid)->get();
        $this->assertEquals($this->auditActor->actorId, $activity->first()->causer_id);
        $this->assertEquals($this->auditActor->actorClass, $activity->first()->causer_type);
        $this->assertEquals($this->auditActor->properties, $activity->first()->properties->toArray());
        $this->assertEquals($this->auditResourceClass, $activity->first()->subject_type);
        $this->assertEquals($this->auditResourceIds[0], $activity->first()->subject_id);
        $this->assertEquals($this->auditDescription, $activity->first()->description);
        $this->assertEquals('auditable_test', $activity->first()->event);
        $this->assertEquals($this->auditBatchUuid, $activity->first()->batch_uuid);

        $this->assertEquals($this->auditActor->actorId, $activity->last()->causer_id);
        $this->assertEquals($this->auditActor->actorClass, $activity->last()->causer_type);
        $this->assertEquals($this->auditActor->properties, $activity->last()->properties->toArray());
        $this->assertEquals($this->auditResourceClass, $activity->last()->subject_type);
        $this->assertEquals($this->auditResourceIds[1], $activity->last()->subject_id);
        $this->assertEquals($this->auditDescription, $activity->last()->description);
        $this->assertEquals('auditable_test', $activity->last()->event);
        $this->assertEquals($this->auditBatchUuid, $activity->last()->batch_uuid);
    }
}
