import express from 'express';
import {Server as SocketIOServer} from 'socket.io';
import {createServer} from "node:http";
import path from 'path';
import {AC, Gate, Light} from "./data/devices.js";

const __dirname = path.resolve(path.dirname(''));
let devices = [];

function createDevice(typeId, deviceName) {
    let device = {};

    switch (typeId) {
        case 1:
            device = new AC;
            break;
        case 2:
            device = new Gate;
            break;
        case 3:
            device = new Light;
            break;
    }

    device.name = deviceName;
    device.id = devices.length + 1;

    devices.push(device);

    console.log(devices);
}

const app = express();
const server = createServer(app);
const io = new SocketIOServer(server, {cors: {origin: '*'}});

app.use(express.json());

io.on('connection', (socket) => {
    console.log('New connection');
})

app.get('/', (req, res) => {
    res.sendFile(__dirname + '/src/views/index.html');
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

app.get('/devices/:id', (req, res) => {
    const deviceId = Number(req.params.id);
    const device = devices.find(device => device.id === deviceId);
    res.json(device);
});

app.post('/devices/:id/update-parameter', (req, res) => {
    const deviceId = Number(req.params.id);
    const {parameter, value} = req.body;

    const idx = devices.findIndex(device => device.id === deviceId);

    if (idx === -1) {
        return res.status(404).json({"error": 'Device not found'});
    }

    devices[idx].data.find(data => data.name === parameter).value = value;

    const device = devices[idx];
    res.json(device);

    io.emit('deviceParameterChanged', device);
});


const Port = 3000;
server.listen(Port, () => {
    console.log(`Device manager is running on http://localhost:${Port}`);
});
