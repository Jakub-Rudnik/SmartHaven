<?php

declare(strict_types=1);

namespace Entity;

class Device
{
    private int $id;
    private string $name;
    private DeviceType $type;
    private ?string $room;
    private bool $state;
    private ?string $location;

    public function __construct(int $id, string $name, DeviceType $type, ?string $room, bool $state, ?string $location)
    {
        $this->id = $id;
        $this->name = $name;
        $this->type = $type;
        $this->room = $room;
        $this->state = $state;
        $this->location = $location;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getRoom(): ?string
    {
        return $this->room;
    }

    public function getState(): bool
    {
        return $this->state;
    }

    public function getType(): DeviceType
    {
        return $this->type;
    }
    public function getLocation(): ?string
    {
        return $this->location;
    }
}
?>