<?php

namespace UI;

use Interfaces\UIElement;
use Lib\DatabaseConnection;
use Services\DeviceService;

class Groups implements UIElement
{
    private DeviceService $deviceService;
    private array $devices = [];

    public function __construct(DatabaseConnection $db)
    {
        $this->deviceService = new DeviceService($db);
        $this->devices = $this->deviceService->getDevicesGroupedByLocations();
    }

    public function render(): string
    {
        $header = new Header('Grupy urządzeń');
        $html = $header->render();
        $html .= "<div class='d-flex flex-column py-5 gap-3'>";

        $rooms = [];

        foreach ($this->devices as $device) {
            $roomName = $device->getRoom();

            if (!isset($rooms[$roomName])) {
                $rooms[$roomName] = [];
            }

            $rooms[$roomName][] = $device;
        }

        foreach ($rooms as $roomName => $devices) {
            $html .= "<div>";
            $html .= "<h2>" . htmlspecialchars($roomName) . "</h2>";
            $html .= "<div class='d-grid gap-4 devices py-3'>";

            foreach ($devices as $device) {
                $toggleBtn = new ToggleDevice($device);
                $deviceType = $device->getType();

                $html .= "
                    <div class='rounded-4 p-4 device-card d-flex gap-2 justify-content-between align-items-center text-decoration-none text-white'>
                        <div class='d-flex flex-column justify-content-center'>
                            <h3 class='mb-0 text-truncate'>" . htmlspecialchars($device->getName()) . "</h3>
                            <p class='mb-0 text-secondary'>" . htmlspecialchars($deviceType->getName()) . "</p>
                        </div>
                        " . $toggleBtn->render() . "
                    </div>
                ";
            }
            $html .= "</div><hr></div>";
        }

        $html .= "</div>";
        return $html;

    }
}