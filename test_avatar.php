<?php

require 'vendor/autoload.php';

use Intervention\Image\Typography\Font;

try {
    $font = new Font('');
    $font->valign('middle');
} catch (\Throwable $e) {
    echo $e->getMessage() . "\n";
}
