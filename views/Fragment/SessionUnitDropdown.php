<?php

$units = \Aventura\Edd\Bookings\Utils\UnitUtils::getPluralUnitLabels();
$data['items'] = $units;
echo eddBookings()->renderView('Fragment.Dropdown', $data);
