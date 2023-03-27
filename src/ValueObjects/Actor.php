<?php

namespace OnrampLab\AuditingLog\ValueObjects;

use JsonSerializable;

class Actor implements JsonSerializable
{
    /**
     * Caused by which model id
     */
    public int|string|null $actorId;

    /**
     * Caused by which model class
     */
    public ?string $actorClass;

    /**
     * Custom data to log
     */
    public array $properties;

    public function __construct(array $data)
    {
        $this->actorId = data_get($data, 'actorId');
        $this->actorClass = data_get($data, 'actorClass');
        $this->properties = data_get($data, 'properties', []);
    }

    public function toArray(): array
    {
        return [
            'actorId' => $this->actorId,
            'actorClass' => $this->actorClass,
            'properties' => $this->properties,
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
