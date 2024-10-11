<?php
declare(strict_types=1);

require_once './Entity/DeviceType.php';

class Device {
    private int $id;
    private string $name;
    private DeviceType $type;
    private bool $state;

    public function __construct(int $id, string $name, DeviceType $type, bool $state) {
        $this->id = $id;
        $this->name = $name;
        $this->type = $type;
        $this->state = $state;
    }

    public function getId(): int {
        return $this->id;
    }

    public function getName(): string {
        return $this->name;
    }

    public function getType(): DeviceType {
        return $this->type;
    }

    public function getState(): bool {
        return $this->state;
    }
}