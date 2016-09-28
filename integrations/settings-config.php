<?php

/**
 * A simple interal integration that loads a configuration file of sections and options for the Plugin's settings.
 */

// Load config file
$config = eddBookings()->loadConfigFile('settings');
// If valid, register
if (is_array($config)) {
    eddBookings()->getSettings()->addSections($config);
}
