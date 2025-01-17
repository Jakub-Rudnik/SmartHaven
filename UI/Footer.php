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
               
            </body>
            </html>
       ';
    }
}