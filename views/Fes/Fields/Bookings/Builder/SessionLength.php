<?php

$base = $data['base'];
$key = 'session_length';
$name = __('"Session Length" Option', 'eddbk');

echo $base($key, $name, $data);
