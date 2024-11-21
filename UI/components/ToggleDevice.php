<?php

namespace UI\components;

use Entity\Device;
use Interfaces\UIElement;


class ToggleDevice implements UIElement
{
    private Device $device;

    public function __construct(Device $device)
    {
        $this->device = $device;
    }

    function render(): string
    {
        $html = "";

        $status = $this->device->getState();
        $toggleText = $status == 1 ? 'Wyłącz' : 'Włącz';
        $newStatus = $status ? 0 : 1;

        $html .= "
        <button class='btn btn-secondary toggleDevice' data-device-id='".$this->device->getId()."' data-new-status='".$newStatus."'>".$toggleText."</button>
        ";

        return $html;
    }
}

?>



