<?php

$base = $data['base'];
$key = 'session_length';
$name = __('"Session Length" Option', 'eddk');

echo $base($key, $name, $data);
