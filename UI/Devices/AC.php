<?php

namespace UI\Devices;

use Entity\Device;
use Interfaces\UIElement;

class AC implements UIElement
{
    private Device $device;

    public function __construct(Device $device)
    {
        $this->device = $device;
    }

    public function render(): string
    {
        $statusID = uniqid();
        $tempID = uniqid();
        $modeID = uniqid();

        return "
        <div class='d-grid w-100 devices'>
            <div class='position-relative device-card-top p-4 card d-flex flex-column justify-content-end align-items-start text-decoration-none text-white device-image w-100 shadow-lg'>
                <h3 class='text-white z-3'><strong>" . $this->device->getName() . "</strong></h3>
                <h5 class='text-secondary z-3'>" . $this->device->getType()->getDescription() . "</h5>
            </div>
            <div class='rounded-start-0 device-card-bottom p-4 card d-flex flex-column gap-3 justify-content-between align-items-start text-decoration-none text-white w-100 shadow-lg'>
                    <div class='d-flex w-100 py-3 justify-content-between align-items-center'>
                        <h5 class='mb-0 text-truncate'>Status</h5>
                        <div class='d-flex gap-2 form-check form-switch'>
                            <input type='checkbox' class='btn-check' id='" . $statusID . "-value' autocomplete='off'>
                            <label class='btn' for='" . $statusID . "-value' id='" . $statusID . "-label'> - </label>
                        </div>
                    </div>
                    <div class='d-flex w-100 py-3 justify-content-between align-items-center'>
                        <h5 class='mb-0 text-truncate'>Temperatura</h5>
                        <div class='d-flex gap-2 align-items-center'>
                            <button class='btn btn-secondary' id='" . $tempID . "-minus'>-</button>
                            <p class='m-0 text-secondary px-2' id='" . $tempID . "-value'> - °C</p>
                            <button class='btn btn-secondary' id='" . $tempID . "-plus'>+</button>
                        </div>
                    </div>
                    <div class='d-flex w-100 py-3 justify-content-between align-items-center'>
                        <h5 class='mb-0 text-truncate'>Tryb</h5>
                        <p class='m-0 text-secondary' id='" . $modeID . "-value'> -
                        </p>
                    </div>
                </div>
            </div>
            
            
            <script type='module'>
                const socket = io('http://localhost:3000');
                const deviceUrl = '" . $this->device->getUrl() . "';
                const deviceId = Number(deviceUrl.split('/').pop());

                function setParameters(data) { 
                    data.forEach((parameter) => {
                       switch (parameter.name) {
                           case 'status':
                               document.getElementById('" . $statusID . "-value').checked = parameter.value;
                               document.getElementById('" . $statusID . "-label').textContent = parameter.value ? 'Włączona' : 'Wyłączona';
                               document.getElementById('" . $statusID . "-label').classList.toggle('btn-success', parameter.value);
                               document.getElementById('" . $statusID . "-label').classList.toggle('btn-secondary', !parameter.value);                           
                               break;
                           case 'temperature':
                               document.getElementById('" . $tempID . "-value').textContent = parameter.value + '°C';
                               break;
                           case 'mode':
                               document.getElementById('" . $modeID . "-value').textContent = parameter.value;
                               break;
                       }
                    });
                }
                
                if (deviceUrl && deviceId) {
                    const response = await fetch(deviceUrl);
                    const data = await response.json();
                    
                    if (response.ok) {
                        setParameters(data.data);
                    }
                    
                    document.getElementById('" . $statusID . "-value').addEventListener('change', async () => {
                        const response = await fetch(deviceUrl + '/update-parameter', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({
                                parameter: 'status',
                                value: Number(document.getElementById('" . $statusID . "-value').checked)
                            })
                        });
                        
                        const data = await response.json();
                        
                        if (response.ok) {
                           const parameter = data.data.find((parameter) => parameter.name === 'status');
                           document.getElementById('" . $statusID . "-value').checked = parameter.value;
                           document.getElementById('" . $statusID . "-label').textContent = parameter.value ? 'Włączona' : 'Wyłączona';
                           document.getElementById('" . $statusID . "-label').classList.toggle('btn-success', parameter.value);
                           document.getElementById('" . $statusID . "-label').classList.toggle('btn-secondary', !parameter.value);
                        }
                    }); 
                    
                    document.getElementById('" . $tempID . "-minus').addEventListener('click', async () => {
                        const response = await fetch(deviceUrl + '/update-parameter', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({
                                parameter: 'temperature',
                                value: Number(document.getElementById('" . $tempID . "-value').innerText.split('°')[0]) - 1
                            })
                        });
                        
                        const data = await response.json();
                        
                        if (response.ok) {
                           const parameter = data.data.find((parameter) => parameter.name === 'temperature');
                           document.getElementById('" . $tempID . "-value').innerText = parameter.value + '°C';
                        }
                    }); 
                    
                    document.getElementById('" . $tempID . "-plus').addEventListener('click', async () => {
                        const response = await fetch(deviceUrl + '/update-parameter', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({
                                parameter: 'temperature',
                                value: Number(document.getElementById('" . $tempID . "-value').innerText.split('°')[0]) + 1
                            })
                        });
                        
                        const data = await response.json();
                        
                        if (response.ok) {
                           const parameter = data.data.find((parameter) => parameter.name === 'temperature');
                           document.getElementById('" . $tempID . "-value').innerText = parameter.value + '°C';
                        }
                    });
                     
                    socket.emit('joinRoom', deviceId);

                    socket.on('roomJoined', (data) => {
                       if (data.success) {
                            showToastMessage('Pomyślnie połączono z urządzeniem ' + '" . $this->device->getName() . "' + ' (' + data.data.name + ')') 
                       }
                       
                    });

                    socket.on('deviceParameterChanged', (device) => {
                        showToastMessage('Parametr urządzenia został zmieniony');
                        setParameters(device.data); 
                    }); 
                }
                
            </script>
        </div>
       ";
    }
}