<?php

use Intervention\Image\Typography\FontFactory;
require 'vendor/autoload.php';

try {
    $factory = new FontFactory(function() {});
    $factory->align('left', 'bottom');
    echo "align('left', 'bottom') OK\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
