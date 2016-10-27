<?php

use \Aventura\Diary\DateTime\Duration;
use \Aventura\Edd\Bookings\Model\Service;
use \Aventura\Edd\Bookings\Renderer\AvailabilityRenderer;

/* @var $service Service */
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

$options = $data['characteristics']['options'];

$hideOnDisabledClass = ((bool) $options['bookings_enabled']['hide_others'])
    ? 'edd-bk-hide-if-bookings-disabled'
    : '';

$namePrefix = 'edd-bk-';

\wp_nonce_field('edd_bk_save_meta', 'edd_bk_service');

// Enable Bookings
if ((bool)($options['bookings_enabled']['enabled'])): ?>
<div class="edd-bk-fes-field">
    <label>
        <input type="hidden" name="<?= $namePrefix ?>bookings-enabled" value="0" />
        <input
            id="edd-bk-bookings-enabled"
            type="checkbox"
            name="<?= $namePrefix ?>bookings-enabled"
            value="1" <?= checked($bookingsEnabled, true) ?>
            />
            <?= $options['bookings_enabled']['label'] ?>
    </label>
</div>
<?php else: ?>
<div class="edd-bk-fes-field">
    <input
        id="edd-bk-bookings-enabled"
        type="hidden"
        value="<?= ($bookingsEnabled? '1' : '0') ?>"
        />
</div>
<?php endif; ?>

<?php // Session Length
if ((bool)($options['session_length']['enabled'])): ?>
<div class="edd-bk-fes-field <?= $hideOnDisabledClass ?>" >
    <label>
        <?= $options['session_length']['label'] ?>
        <input
            type="number"
            name="<?= $namePrefix ?>session-length"
            value="<?= $sessionLength ?>"
            />
        <select name="<?= $namePrefix ?>session-unit">
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
if ((bool)($options['min_max_sessions']['enabled'])): ?>
<div class="edd-bk-fes-field <?= $hideOnDisabledClass ?>" >
    <label>
        <?= $options['min_max_sessions']['label'] ?>
        <input
            type="number"
            name="<?= $namePrefix ?>min-sessions"
            value="<?= $minSessions ?>"
            min="1"
            />
        <?= _x('to', 'From x sessions to y sessions' , 'eddbk') ?>
        <input
            type="number"
            name="<?= $namePrefix ?>max-sessions"
            value="<?= $maxSessions ?>"
            min="1"
            />
        <?= _x('sessions', 'From x sessions to y sessions' , 'eddbk') ?>
    </label>
</div>
<?php endif; ?>

<?php // Session Cost
if ((bool)($options['session_cost']['enabled'])): ?>
<div class="edd-bk-fes-field <?= $hideOnDisabledClass ?>" >
    <label>
        <?= $options['session_cost']['label'] ?>
        <?= edd_currency_symbol(); ?>
        <input
            type="number"
            name="<?= $namePrefix ?>session-cost"
            value="<?= $sessionCost ?>"
            min="0"
            step="0.01"
            />
    </label>
</div>
<?php endif; ?>

<?php // Availability
if ((bool)($options['availability']['enabled'])): ?>
<div class="edd-bk-fes-field <?= $hideOnDisabledClass ?>" >
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
if ((bool)($options['use_customer_tz']['enabled'])): ?>
<div class="edd-bk-fes-field <?= $hideOnDisabledClass ?>" >
    <label>
        <input
            type="checkbox"
            name="<?= $namePrefix ?>use-customer-tz"
            value="on" <?= checked($customerTz, true) ?>
            />
            <?= $options['use_customer_tz']['label'] ?>
    </label>
</div>
<?php endif; ?>

<?php
$singlePageOutputDefault = eddBookings()->getSettings()->getSection('fes')->getOption('single_page_output_default')->getValue();
// Default value for single page output option
?>
<input type="hidden" name="<?= $namePrefix; ?>single-page-output" value="<?= $singlePageOutputDefault; ?>" />
