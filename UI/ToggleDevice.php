<?php

namespace UI;

use Entity\Device;
use Interfaces\UIElement;

class ToggleDevice implements UIElement
{
    private Device $device;

    public function __construct(Device $device)
    {
        $this->device = $device;
    }

    public function render(): string
    {
        // Uzyskujemy aktualny status jako bool
        $status = $this->device->getStatus(); 
        // Tekst przycisku w zależności od obecnego statusu
        $toggleText = $status ? 'Wyłącz' : 'Włącz';
        // Nowy status (0 / 1) – przekazywany w atrybucie data-new-status
        $newStatus = $status ? 0 : 1;

        $html = "
            <button 
                class='btn btn-secondary toggleDevice' 
                data-device-id='{$this->device->getId()}'
                data-new-status='{$newStatus}'
            >
                {$toggleText}
            </button>
        ";

        return $html;
    }
}
