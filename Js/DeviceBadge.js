export default class DeviceBadge {
    constructor({id, name, container}) {
        this.id = id;
        this.name = name;
        this.container = container;
        this.socket = io('http://localhost:3000');

        this.statusID = `${id}-status`;

        this.init();
    }

    init() {
        this.render();
        this.setupSocketListeners();
    }

    render() {
        this.container.innerHTML = `
            <div class="d-flex align-items-center gap-2">
                <span>${this.name}</span>
                <span id="${this.statusID}" class="status-indicator" style="width: 10px; height: 10px; border-radius: 50%; background-color: red;"></span>
            </div>
        `;
    }

    updateStatusIndicator(status) {
        const indicator = document.getElementById(this.statusID);
        if (indicator) {
            indicator.style.backgroundColor = status ? 'green' : 'red';
        }
    }

    setupSocketListeners() {
        this.socket.emit('joinRoom', this.id);

        // Aktualizacja wskaźnika stanu w czasie rzeczywistym
        this.socket.on('deviceParameterChanged', (device) => {
            if (device.id === this.id && device.data.some(param => param.name === 'status')) {
                const status = device.data.find(param => param.name === 'status').value;
                this.updateStatusIndicator(status);
            }
        });
    }
}
