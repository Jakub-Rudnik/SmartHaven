<?php

namespace SmartHaven\UI\components;

use SmartHaven\Interfaces\UIElement;

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
