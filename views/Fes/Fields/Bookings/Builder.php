<?php

if (!defined('EDD_BK_BOOKINGS_FIELD_BUILDER_VIEW_NAMESPACE')) {
    define('EDD_BK_BOOKINGS_FIELD_BUILDER_VIEW_NAMESPACE', 'Fes.Fields.Bookings.Builder');
}

$options = $data['characteristics']['options'];

/**
 * The base option view renderer function. Renders the option base view.
 * 
 * @param string $key The save key of the option.
 * @param string $name The name of the option.
 * @param array $data The view data array.
 * @return string The rendered view content.
 */
$baseOptionView = function($key, $name, $data)
{
    $merged = array_merge($data, compact('name', 'key'));
    $viewName = sprintf('%s.Base', EDD_BK_BOOKINGS_FIELD_BUILDER_VIEW_NAMESPACE);
    return eddBookings()->renderView($viewName, $merged);
};

/**
 * Used to pass all the required information to sub-views.
 * 
 * @param string $index The index of the option.
 * @return array An array of data to be passed to a sub-view.
 */
$optionData = function($index) use ($data, $options, $baseOptionView)
{
    $extra = array(
        'index' => $data['index'],
        'base'  => $baseOptionView
    );
    return array_merge($options[$index], $extra);
};

/**
 * Renders a subview.
 * 
 * @param string $subViewName The subview name.
 * @param string $optionIndex The index of the option.
 * @return string The rendered subview content.
 */
$renderSubView = function($subViewName, $optionIndex, $required = false) use ($optionData)
{
    $subView = sprintf('%s.%s', EDD_BK_BOOKINGS_FIELD_BUILDER_VIEW_NAMESPACE, $subViewName);
    $data    = array_merge($optionData($optionIndex), compact('required'));

    return eddBookings()->renderView($subView, $data);
}

?>

<hr/>
<?= $renderSubView('EnableBookings', 'bookings_enabled'); ?>
<hr/>
<?=  $renderSubView('SessionLength', 'session_length', true); ?>
<hr/>
<?=  $renderSubView('MinMaxSessions', 'min_max_sessions', true); ?>
<hr/>
<?=  $renderSubView('SessionCost', 'session_cost', true); ?>
<hr/>
<?=  $renderSubView('Availability', 'availability'); ?>
<hr/>
<?=  $renderSubView ('UseCustomerTz', 'use_customer_tz'); ?>
