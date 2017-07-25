<?php

$base = $data['base'];
$data['required'] = true;
$key = 'availability';
$name = __('"Availability" Option', 'eddbk');

echo $base($key, $name, $data);
