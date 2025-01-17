class AC {
    id = 0;
    name = '';
    type = 'AC';
    data = [
        {
            name: 'status',
            value: 0
        },
        {
            name: 'temperature',
            value: 22
        }
    ]
}

class Gate {
    id = 0;
    name = '';
    type = 'Gate';
    data = [
        {
            name: 'status',
            value: 0
        }
    ]
}

class Light {
    id = 0;
    name = '';
    type = 'Light';
    data = [
        {
            name: 'status',
            value: 0
        },
        {
            name: 'brightness',
            value: 50
        }
    ]
}

export {AC, Gate, Light};
