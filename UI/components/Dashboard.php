<?php

namespace UI\components;

use Interfaces\UIElement;

class Dashboard implements UIElement
{

    public function render(): string
    {
        $html = "";
        $header = new Header('Witaj Dawid! 😎');
        $html .= $header->render();

        return $html;
    }
}
