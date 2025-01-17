export default class Light {
    constructor(id, simulationId, name, url, container) {
        this.id = id;
        this.simulationId = simulationId;
        this.name = name;
        this.url = url;
        this.container = container;
        this.socket = io('http://localhost:3000');

        this.statusID = `${id}-status`;
        this.brightnessID = `${id}-brightness`;

        this.init();
    }

    async init() {
        // Pobierz dane urządzenia
        const response = await fetch(this.url);
        const data = await response.json();
        if (response.ok) {
            this.render();
            this.setParameters(data.data);
            this.setupSocketListeners();
        }

        this.setupEventListeners();
    }

    render() {
        this.container.innerHTML = `
            <div class='d-grid w-100 devices'>
                <div class='position-relative device-card-top p-4 d-flex justify-content-between align-items-end text-decoration-none text-white device-image device-image-light shadow-lg border border-secondary-subtle'>
                    <div class="d-flex flex-column justify-content-start align-items-start z-3">
                        <h3 class='text-white'><strong>${this.name}</strong></h3>
                        <h5 class='text-secondary'>Lampa</h5>
                    </div>
                    <div class="d-flex justify-content-end align-items-center gap-2 z-3">
                       <a href="/app/devices/update/${this.id}" class="btn btn-secondary">
                       <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil-fill" viewBox="0 0 16 16">
  <path d="M12.854.146a.5.5 0 0 0-.707 0L10.5 1.793 14.207 5.5l1.647-1.646a.5.5 0 0 0 0-.708zm.646 6.061L9.793 2.5 3.293 9H3.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.207zm-7.468 7.468A.5.5 0 0 1 6 13.5V13h-.5a.5.5 0 0 1-.5-.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.5-.5V10h-.5a.5.5 0 0 1-.175-.032l-.179.178a.5.5 0 0 0-.11.168l-2 5a.5.5 0 0 0 .65.65l5-2a.5.5 0 0 0 .168-.11z"/>
</svg>
                          </a>  
                          <a href="/app/devices/delete/${this.id}" class="btn btn-danger">
                       <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash-fill" viewBox="0 0 16 16">
  <path d="M2.5 1a1 1 0 0 0-1 1v1a1 1 0 0 0 1 1H3v9a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V4h.5a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H10a1 1 0 0 0-1-1H7a1 1 0 0 0-1 1zm3 4a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 .5-.5M8 5a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7A.5.5 0 0 1 8 5m3 .5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 1 0"/>
</svg>
                          </a> 
                    </div>
                </div>
                <div class='rounded-start-0 device-card-bottom p-4 d-flex flex-column gap-3 justify-content-between align-items-start text-decoration-none text-white w-100 shadow-lg card border border-secondary-subtle'>
                    <div class='d-flex w-100 py-3 justify-content-between align-items-center'>
                        <h5 class='mb-0 text-truncate'>Status</h5>
                        <div class='d-flex gap-2 form-check form-switch'>
                            <input type='checkbox' class='btn-check' id='${this.statusID}-value' autocomplete='off'>
                            <label class='btn' for='${this.statusID}-value' id='${this.statusID}-label'> - </label>
                        </div>
                    </div>
                    <div class='d-flex w-100 py-3 justify-content-between align-items-center'>
                        <h5 class='mb-0 text-truncate'>Jasność</h5>
                        <div class='d-flex gap-2 align-items-center flex-nowrap'>
                            <input type="range" class="form-range" min="0" max="100" id="${this.brightnessID}-value"> 
                            <label for="${this.brightnessID}-value" id="${this.brightnessID}-label" class="form-label flex-1"> - </label>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    setParameters(parameters) {
        parameters.forEach((parameter) => {
            switch (parameter.name) {
                case 'status':
                    const statusCheckbox = document.getElementById(`${this.statusID}-value`);
                    const statusLabel = document.getElementById(`${this.statusID}-label`);
                    statusCheckbox.checked = parameter.value;
                    statusLabel.textContent = parameter.value ? 'Włączona' : 'Wyłączona';
                    statusLabel.classList.toggle('btn-success', parameter.value);
                    statusLabel.classList.toggle('btn-secondary', !parameter.value);
                    break;
                case 'brightness':
                    document.getElementById(`${this.brightnessID}-value`).value = parameter.value;
                    document.getElementById(`${this.brightnessID}-label`).textContent = parameter.value + ' %';
                    break;
            }
        });
    }

    setupEventListeners() {
        document.getElementById(`${this.statusID}-value`).addEventListener('change', async () => {
            const newValue = Number(document.getElementById(`${this.statusID}-value`).checked);
            const response = await fetch(`${this.url}/update-parameter`, {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({parameter: 'status', value: newValue}),
            });
            const data = await response.json();
            this.setParameters(data.data);
        });

        document.getElementById(`${this.brightnessID}-value`).addEventListener('change', async () => {
            const currentValue = document.getElementById(`${this.brightnessID}-value`).value;
            const response = await fetch(`${this.url}/update-parameter`, {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({parameter: 'brightness', value: currentValue}),
            });
            const data = await response.json();
            this.setParameters(data.data);
        });
    }

    setupSocketListeners() {
        this.socket.emit('joinRoom', this.id);

        this.socket.on('deviceParameterChanged', (device) => {
            console.log(device);
            if (device.id === this.simulationId) {
                this.setParameters(device.data);
            }
        });
    }
}
