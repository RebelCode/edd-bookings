<?php

$base = $data['base'];
$key = 'min_max_sessions';
$name = __('"Min/Max Sessions" Option', 'eddbk');

echo $base($key, $name, $data);
