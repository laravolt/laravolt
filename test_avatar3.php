<?php
require 'vendor/autoload.php';

use Intervention\Image\Typography\Font;

$font = new Font('');
try {
    $font->setAlignment('middle');
} catch (\Throwable $e) {
    echo "Exception: " . get_class($e) . " - " . $e->getMessage() . "\n";
}
