<?php

$base = $data['base'];
$key = 'session_cost';
$name = __('"Session Cost" Option', 'eddbk');

echo $base($key, $name, $data);
