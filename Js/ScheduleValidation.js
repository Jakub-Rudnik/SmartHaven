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

        // Walidacja czasu
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

    everydayCheckbox.addEventListener('change', function () {
        toggleAllWeekdays(everydayCheckbox.checked);
        validateForm();
    });

    weekdaysCheckboxes.forEach(cb => {
        cb.addEventListener('change', function () {
            if (!this.checked) {
                everydayCheckbox.checked = false;
            }
            validateForm();
        });
    });

    deviceSelect.addEventListener('change', validateForm);
    startTime.addEventListener('input', validateForm);
    endTime.addEventListener('input', validateForm);
});
