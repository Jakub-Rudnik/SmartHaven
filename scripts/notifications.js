document.getElementById('updateDeviceBtn').addEventListener('click', function() {
    fetch('update_state.php', {
        method: 'POST'
        // W razie potrzeby można przekazać w body np. deviceId, newState itp.
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'ok') {
            // Wyświetlamy powiadomienie toast
            Toastify({
                text: data.message,
                duration: 5000, // Toast zniknie po 5 sekundach
                close: true,    // Dodanie przycisku zamykania
                gravity: "top", // położenie toastu: top lub bottom
                position: "right", // lewo/prawo
                style: {
                    background: "#4CAF50"
                  },
                stopOnFocus: true // pauza przy najechaniu myszką
            }).showToast();
        } else {
            // Obsługa błędu
            Toastify({
                text: data.message,
                duration: 5000,
                close: true,
                gravity: "top",
                position: "right",
                style: {
                    background: "#F44336"
                  }
            }).showToast();
        }
    })
    .catch(error => {
        console.error('Błąd komunikacji:', error);
        // Ewentualny toast błędu
        Toastify({
            text: 'Wystąpił błąd podczas komunikacji z serwerem.',
            duration: 5000,
            close: true,
            gravity: "top",
            position: "right",
            style: {
                background: "#F44336"
              }
        }).showToast();
    });
});
