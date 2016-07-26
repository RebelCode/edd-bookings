<?php

$base = $data['base'];
$key = 'bookings_enabled';
$name = __('"Enable Bookings" Option', 'eddk');

echo $base($key, $name, $data);
