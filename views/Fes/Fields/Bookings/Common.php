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
$minSessions = $data['meta']['min_sessions'];
$maxSessions = $data['meta']['max_sessions'];

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

<?php if (boolval($data['options']['min_max_sessions']['enabled'])): ?>
    <label>
        <?= _x('From', 'From x sessions to y sessions' , 'eddbk') ?>
        <input
            type="number"
            name="<?= $data['name'] ?>[min_sessions]"
            value="<?= $minSessions ?>"
            />
        <?= _x('To', 'From x sessions to y sessions' , 'eddbk') ?>
        <input
            type="number"
            name="<?= $data['name'] ?>[max_sessions]"
            value="<?= $maxSessions ?>"
            />
        <?= _x('Sessions', 'From x sessions to y sessions' , 'eddbk') ?>
        <?= $data['options']['min_max_sessions']['label'] ?>
    </label>
<?php endif; ?>
