// scripts/notifications.js

// Function to fetch notifications
function fetchNotifications() {
    fetch('./getNotification.php')
        .then(response => response.json())
        .then(data => {
            const notificationsDiv = document.getElementById('notifications');
            notificationsDiv.innerHTML = ''; // Clear existing notifications

            data.forEach(notification => {
                const notifElement = document.createElement('div');
                notifElement.classList.add('notification');

                const stateText = notification.NewState == '1' ? 'ON' : 'OFF';
                notifElement.textContent = `Urządzenie ${notification.DeviceName} zmieniło status na ${stateText} o ${notification.Timestamp}`;
                notificationsDiv.appendChild(notifElement);
            });
        })
        .catch(error => console.error('Error fetching notifications:', error));
}

// Fetch notifications every 5 seconds
setInterval(fetchNotifications, 5000);
fetchNotifications(); // Initial fetch
