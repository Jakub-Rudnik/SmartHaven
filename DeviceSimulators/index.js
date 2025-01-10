const express = require('express');

let devices = [];

function createDevice(typeId, deviceName) {
    let type = '';

    switch (typeId) {
        case 1:
            type = 'ac';
            break;
        case 2:
            type = 'gate';
            break;
        default:
            type = 'unknown';
            break;
    }

    const device = {
        id: devices.length + 1,
        type: type,
        name: deviceName,
        status: 'offline',
    };

    devices.push(device);
}

const app = express();
app.use(express.json());

app.get('/', (req, res) => {
    res.sendFile(__dirname + '/views/index.html');
});

app.post('/create-device', (req, res) => {
    const {name, type} = req.body;
    if (!type || !name) {
        return res.status(400).send('Please specify a valid type and name.');
    }
    createDevice(type, name);
    const device = devices[devices.length - 1];
    res.json(device);
});

app.get('/devices', (req, res) => {
    res.json(devices);
});

app.get('/device/:id', (req, res) => {
    const deviceId = Number(req.params.id);
    const device = devices.find(device => device.id === deviceId);
    res.json(device);
});

app.post('/device/:id/update-status', (req, res) => {
    const deviceId = Number(req.params.id);
    const idx = devices.findIndex(device => device.id === deviceId);
    devices[idx].status = devices[idx].status === 'online' ? 'offline' : 'online';
    const device = devices[idx];
    res.json(device);
});


const Port = 3000;
app.listen(Port, () => {
    console.log(`Device manager is running on http://localhost:${Port}`);
});
