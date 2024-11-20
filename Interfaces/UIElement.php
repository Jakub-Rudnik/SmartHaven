<?php
declare(strict_types=1);

namespace Interfaces;

interface UIElement
{
    public function render(): string;
}
