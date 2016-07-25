<?php

use \Aventura\Diary\DateTime\Duration;
use \Aventura\Edd\Bookings\Model\Availability;
use \Aventura\Edd\Bookings\Renderer\AvailabilityRenderer;

$options = $data['options'];
// Enable bookings
$bookingsEnabled = isset($data['meta']['bookings_enabled'])
    ? $data['meta']['bookings_enabled']
    : $options['bookings_enabled']['default'];
// Session unit
$sessionUnit = isset($data['meta']['session_unit'])
    ? $data['meta']['session_unit']
    : $options['session_unit']['default']['unit'];
// Session Length
$sessionLengthMeta = isset($data['meta']['session_length'])
    ? $data['meta']['session_length']
    : $options['sessions_length']['default']['length'];
$singleSessionLength = Duration::$sessionUnit(1, false);
$sessionLength = $sessionLengthMeta / $singleSessionLength;
// Min Num Sessions
$minSessions = isset($data['meta']['min_sessions'])
    ? $data['meta']['min_sessions']
    : $options['min_max_sessions']['default']['min'];
// Max Num Sessions
$maxSessions = isset($data['meta']['max_sessions'])
    ? $data['meta']['max_sessions']
    : $options['min_max_sessions']['default']['max'];
// Session Cost
$sessionCost = isset($data['meta']['session_cost'])
    ? $data['meta']['session_cost']
    : $options['session_cost']['default'];
// Availability
$availability = is_null($data['service'])
    ? new Availability($data['save_id'])
    : $data['service']->getAvailability()->getTimetable();
// Customer Timezone
$customerTimezone = isset($data['meta']['use_customer_tz'])
    ? $data['meta']['use_customer_tz']
    : $options['use_customer_tz']['default'];

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

<?php if (boolval($data['options']['session_cost']['enabled'])): ?>
    <label>
        <?= edd_currency_symbol(); ?>
        <input
            type="number"
            name="<?= $data['name'] ?>[session_cost]"
            value="<?= $sessionCost ?>"
            />
        <?= $data['options']['session_cost']['label'] ?>
    </label>
<?php endif; ?>

<?php // Availability
if (boolval($data['options']['availability']['enabled'])): ?>
<div class="edd-bk-fes-field">
    <p><strong><?= $data['options']['availability']['label'] ?></strong></p>
    <?php
        $availRenderer = new AvailabilityRenderer($availability);
        echo $availRenderer->render(array(
            'doc_link'      => false,
            'timezone_help' => false
        ));
    ?>
</div>
<?php endif; ?>

<?php // Use Customer Timezone
if (boolval($data['options']['use_customer_tz']['enabled'])): ?>
<div class="edd-bk-fes-field">
    <label>
        <input
            type="checkbox"
            name="<?= $data['name'] ?>[use_customer_tz]"
            value="on" <?= checked($customerTimezone, true) ?>
            />
            <?= $data['options']['use_customer_tz']['label'] ?>
    </label>
</div>
<?php endif; ?>
