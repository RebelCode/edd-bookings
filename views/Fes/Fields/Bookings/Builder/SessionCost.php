<?php

$base = $data['base'];
$key = 'session_cost';
$name = __('"Session Cost" Option', 'eddk');

echo $base($key, $name, $data);
