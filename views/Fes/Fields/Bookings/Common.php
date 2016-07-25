<?php

$bookingsEnabled = $data['meta']['bookings_enabled'];

if (boolval($data['options']['bookings_enabled']['enabled'])):
    ?>
    <label>
        <input
            type="checkbox"
            name="<?= $data['name'] ?>[bookings_enabled]"
            value="on" <?= checked($bookingsEnabled, true) ?>
            />
            <?= $data['options']['bookings_enabled']['label'] ?>
    </label>
<?php endif; ?>
