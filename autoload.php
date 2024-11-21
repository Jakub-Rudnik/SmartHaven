<?php

require_once 'Autoloader.php';

// Register your source directory
Autoloader::register('Api', __DIR__.'/Api');
Autoloader::register('Services', __DIR__.'/Services');
Autoloader::register('Lib', __DIR__.'/Lib');
Autoloader::register('Interfaces', __DIR__.'/Interfaces');
Autoloader::register('Entity', __DIR__.'/Entity');
Autoloader::register('UI', __DIR__.'/UI');