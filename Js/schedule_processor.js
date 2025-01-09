// Harmonogram sprawdzania stanu urządzeń
setInterval(() => {
    // Wykonanie zapytania AJAX do endpointa harmonogramów
    fetch('process_schedules.php', {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
        },
    })
        .then((response) => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then((data) => {
            console.log('Schedules processed:', data);

            // Aktualizacja stanu urządzeń na stronie, jeśli dane są dostępne
            if (data.devices) {
                updateDeviceStates(data.devices);
            }
        })
        .catch((error) => {
            console.error('Error processing schedules:', error);
        });
}, 60000); // Wywoływanie co minutę (60000 ms)

// Funkcja aktualizująca stan urządzeń na stronie
function updateDeviceStates(devices) {
    devices.forEach((device) => {
        const deviceElement = document.querySelector(`#device-${device.DeviceID}`);
        if (deviceElement) {
            deviceElement.querySelector('.state').textContent = device.State === '1' ? 'On' : 'Off';
        }
    });
}
