<?php

use \Aventura\Diary\DateTime\Duration;
use \Aventura\Edd\Bookings\Model\Availability;
use \Aventura\Edd\Bookings\Renderer\AvailabilityRenderer;

/* @var $service \Aventura\Edd\Bookings\Model\Service */
$service = $data['service'];
$bookingsEnabled = $service->getBookingsEnabled();
$sessionUnit = $service->getSessionUnit();
$singleSessionLength = Duration::$sessionUnit(1, false);
$sessionLength = $service->getSessionLength() / $singleSessionLength;
$minSessions = $service->getMinSessions();
$maxSessions = $service->getMaxSessions();
$sessionCost = $service->getSessionCost();
$availability = $service->getAvailability()->getTimetable();
$customerTz = $service->getUseCustomerTimezone();

$options = $data['options'];

// Enable Bookings
if (boolval($options['bookings_enabled']['enabled'])): ?>
<div class="edd-bk-fes-field">
    <label>
        <input
            type="checkbox"
            name="<?= $data['name'] ?>[bookings_enabled]"
            value="on" <?= checked($bookingsEnabled, true) ?>
            />
            <?= $options['bookings_enabled']['label'] ?>
    </label>
</div>
<?php endif; ?>

<?php // Session Length
if (boolval($options['session_length']['enabled'])): ?>
<div class="edd-bk-fes-field">
    <label>
        <?= $options['session_length']['label'] ?>
        <input
            type="number"
            name="<?= $data['name'] ?>[session_length]"
            value="<?= $sessionLength ?>"
            />
        <select>
            <?php
            $sessionUnits = array(
                'minutes' => __('minutes', 'eddbk'),
                'hours'   => __('hours', 'eddbk'),
                'days'    => __('days', 'eddbk'),
                'weeks'   => __('weeks', 'eddbk'),
            );
            foreach ($sessionUnits as $key => $val) : ?>
                <option
                    value='<?= $key ?>'
                    <?= selected($key, $sessionUnit, false) ?>
                    >
                    <?= $val ?>
                </option>
            <?php endforeach; ?>
        </select>
    </label>
</div>
<?php endif; ?>

<?php // Min/Max Sessions
if (boolval($options['min_max_sessions']['enabled'])): ?>
<div class="edd-bk-fes-field">
    <label>
        <?= $options['min_max_sessions']['label'] ?>
        <input
            type="number"
            name="<?= $data['name'] ?>[min_sessions]"
            value="<?= $minSessions ?>"
            min="1"
            />
        <?= _x('to', 'From x sessions to y sessions' , 'eddbk') ?>
        <input
            type="number"
            name="<?= $data['name'] ?>[max_sessions]"
            value="<?= $maxSessions ?>"
            min="1"
            />
        <?= _x('sessions', 'From x sessions to y sessions' , 'eddbk') ?>
    </label>
</div>
<?php endif; ?>

<?php // Session Cost
if (boolval($options['session_cost']['enabled'])): ?>
<div class="edd-bk-fes-field">
    <label>
        <?= $options['session_cost']['label'] ?>
        <?= edd_currency_symbol(); ?>
        <input
            type="number"
            name="<?= $data['name'] ?>[session_cost]"
            value="<?= $sessionCost ?>"
            min="0"
            step="0.01"
            />
    </label>
</div>
<?php endif; ?>

<?php // Availability
if (boolval($options['availability']['enabled'])): ?>
<div class="edd-bk-fes-field">
    <p><strong><?= $options['availability']['label'] ?></strong></p>
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
if (boolval($options['use_customer_tz']['enabled'])): ?>
<div class="edd-bk-fes-field">
    <label>
        <input
            type="checkbox"
            name="<?= $data['name'] ?>[use_customer_tz]"
            value="on" <?= checked($customerTz, true) ?>
            />
            <?= $options['use_customer_tz']['label'] ?>
    </label>
</div>
<?php endif; ?>
