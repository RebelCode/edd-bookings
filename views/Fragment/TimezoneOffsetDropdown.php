<?php
$data['items'] = array();
for ($i = -12; $i <= 14; $i += 0.5) {
    $key = $i * 3600;
    $sign = ($i < 0)? '-' : '+';
    $value = sprintf('UTC%1$s%2$s', $sign, abs($i));
    $data['items'][$key] = $value;
}
echo eddBookings()->renderView('Fragment.Dropdown', $data);
