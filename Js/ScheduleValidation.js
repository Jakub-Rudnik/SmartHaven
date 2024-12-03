document.addEventListener('DOMContentLoaded', function () {
    const deviceSelect = document.getElementById('device-select');
    const startTime = document.getElementById('start-time');
    const endTime = document.getElementById('end-time');
    const submitButton = document.getElementById('submit-button');
    const everydayCheckbox = document.getElementById('everyday');
    const weekdaysCheckboxes = document.querySelectorAll('.weekdays');

    function validateForm() {
        const deviceSelected = deviceSelect.value !== '';
        const startTimeValue = startTime.value;
        const endTimeValue = endTime.value;
        const timeValid = startTimeValue && endTimeValue && startTimeValue < endTimeValue;
        const daysSelected = everydayCheckbox.checked || Array.from(weekdaysCheckboxes).some(cb => cb.checked);

        if (!timeValid && startTimeValue && endTimeValue) {
            console.warn('Czas włączenia musi być wcześniejszy niż czas wyłączenia!');
        }

        submitButton.disabled = !(deviceSelected && timeValid && daysSelected);
    }

    function toggleAllWeekdays(checked) {
        weekdaysCheckboxes.forEach(cb => {
            cb.checked = checked;
        });
    }

    function checkEverydayStatus() {
        const allChecked = Array.from(weekdaysCheckboxes).every(cb => cb.checked);
        everydayCheckbox.checked = allChecked; // Automatyczne zaznaczenie „Codziennie”
    }

    everydayCheckbox.addEventListener('change', function () {
        toggleAllWeekdays(everydayCheckbox.checked);
        validateForm();
    });

    weekdaysCheckboxes.forEach(cb => {
        cb.addEventListener('change', function () {
            checkEverydayStatus(); // Sprawdź, czy wszystkie dni są zaznaczone
            validateForm();
        });
    });

    deviceSelect.addEventListener('change', validateForm);
    startTime.addEventListener('input', validateForm);
    endTime.addEventListener('input', validateForm);
});
