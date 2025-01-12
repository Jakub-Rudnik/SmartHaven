<?php

namespace UI;

use Interfaces\UIElement;

class Footer implements UIElement
{

    public function render(): string
    {
        return '
                <script src="/Js/ToggleDevice.js"></script>
                <script src="/Js/ToastMessage.js"></script>
                <script type="module">
                    const socket = io("http://localhost:3000");
                    
                    socket.on("deviceParameterChanged", (device) => {
                        const {name, data} = device;
                        
                        const status = data.find(elem => elem.name === "status").value;
                        
                        showToastMessage(`Zmieniono parametr status na ${status} dla urządzenia ${name}`, status);
                    });
                </script>
            </body>
            </html>
       ';
    }
}