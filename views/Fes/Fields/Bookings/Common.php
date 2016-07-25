<?php

use \Aventura\Diary\DateTime\Duration;

$bookingsEnabled = $data['meta']['bookings_enabled'];
$sessionUnit = $data['meta']['session_unit'];
$sessionLengthMeta = intval($data['meta']['session_length']);
$singleSessionLength = Duration::$sessionUnit(1, false);
$sessionLength = $sessionLengthMeta / $singleSessionLength;
$sessionUnits = array(
    'minutes' => __('minutes', 'eddbk'),
    'hours'   => __('hours', 'eddbk'),
    'days'    => __('days', 'eddbk'),
    'weeks'   => __('weeks', 'eddbk'),
);

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

<?php if (boolval($data['options']['session_length']['enabled'])): ?>
    <label>
        <input
            type="number"
            name="<?= $data['name'] ?>[session_length]"
            value="<?= $sessionLength ?>"
            />
        <select>
            <?php foreach ($sessionUnits as $key => $val) : ?>
                <option
                    value='<?= $key ?>'
                    <?= selected($key, $sessionUnit, false) ?>
                    >
                    <?= $val ?>
                </option>
            <?php endforeach; ?>
        </select>
        <?= $data['options']['session_length']['label'] ?>
    </label>
<?php endif; ?>
