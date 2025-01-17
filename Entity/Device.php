<?php

declare(strict_types=1);

namespace Entity;

class Device
{
    private int $id;
    private string $name;
    private DeviceType $type;

    /**
     * Opcjonalny opis urządzenia (np. dodatkowe informacje,
     * w kodzie często przekazywany pusty string).
     */
    private ?string $description;

    /**
     * Czy urządzenie jest włączone (true) czy wyłączone (false).
     * Wcześniej: getState() -> bool
     */
    private bool $status;

    /**
     * Nazwa grupy / pokoju, do której należy to urządzenie.
     * Może być null, jeśli urządzenie nie należy do żadnej grupy.
     */
    private ?int $room;
    private string $url;

    /**
     * Konstruktor z 6 parametrami, zgodnie z wywołaniami w DeviceService.
     */
    public function __construct(
        int $id,
        string $name,
        DeviceType $type,
        ?string $description,
        ?int $room,
        ?string $url = ''
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->type = $type;
        $this->description = $description;
        $this->room = $room;
        $this->url = $url;
    }

    // ------------------------------
    // Gettery
    // ------------------------------

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Zwraca nazwę grupy/pokoju (może być 'Brak grupy' w logice widoku)
     */
    public function getRoom(): ?int
    {
        return $this->room;
    }

    /**
     * Zwraca (opcjonalny) opis urządzenia.
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getType(): DeviceType
    {
        return $this->type;
    }

    public function getUrl(): string
    {
        return $this->url;
    }
}
