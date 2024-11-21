$toggleDeviceButtons = document.querySelectorAll('.toggleDevice');


$toggleDeviceButtons.forEach(button => button.addEventListener('click', event => toggleDevice(event, button.dataset.deviceId, button.dataset.newStatus)));

function toggleDevice(event, deviceId, status) {
    // Sending an AJAX request to toggleDevice.php
    deviceId = Number(deviceId);
    status = Number(status);

    fetch('/api/toggle-device', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({deviceId: deviceId, status: status})
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Refreshing the page after a status change
                event.target.innerText = data.status ? 'Wyłącz' : 'Włącz';
                event.target.dataset.newStatus = data.status === 0 ? 1 : 0;
            } else {
                console.error('Błąd przy zmianie statusu:', data.message);
            }
        })
        .catch(error => console.error('Błąd:', error));

}


