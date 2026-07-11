<?php
require __DIR__.'/vendor/autoload.php';

$avatar = new \Laravolt\Avatar\Avatar();
$svg = $avatar->create('Jules')->toSvg();
echo 'data:image/svg+xml;base64,' . base64_encode($svg) . "\n";
