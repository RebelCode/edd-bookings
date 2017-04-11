<?php

/**
 * @wordpress-plugin
 * Plugin Name: Easy Digital Downloads - Bookings
 * Plugin URI: https://easydigitaldownloads.com/downloads/edd-bookings/
 * Description: Adds a simple booking system to Easy Digital Downloads.
 * Version: 2.2.1-RC1
 * Author: RebelCode
 * Text Domain: eddbk
 * Domain Path: /languages/
 * License: GPLv3
 */

/**
 * Copyright (C) 2015-2016 RebelCode Ltd.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

// If the file is called directly, or has already been called, abort
if (!defined('WPINC') || defined('EDD_BK')) {
    die;
}

// Plugin File Constant
define('EDD_BK', __FILE__);
// Plugin Version
define('EDD_BK_VERSION', '2.2.1.RC.1');
// Plugin ID, or slug-esque name
define('EDD_BK_PLUGIN_ID', 'eddbk');
// Plugin Name
define('EDD_BK_PLUGIN_NAME', 'EDD Bookings');
// Plugin Author
define('EDD_BK_PLUGIN_AUTHOR', 'RebelCode');
// Parent Plugin Class name
define('EDD_BK_PARENT_PLUGIN_CLASS', 'Easy_Digital_Downloads');
// Parent Plugin Min version required
define('EDD_BK_PARENT_PLUGIN_MIN_VERSION', '2.4.0');
// Minimum WordPress version
define('EDD_BK_MIN_WP_VERSION', '4.0');
// Minimum PHP Version
define('EDD_BK_MIN_PHP_VERSION', '5.3');
// Database version number
define('EDD_BK_DB_VERSION', '1');
// Default text domain
define('EDD_BK_TEXT_DOMAIN', 'eddbk');

// Documentation link
define('EDD_BK_DOCS_URL', 'http://docs.easydigitaldownloads.com/category/1100-bookings');

// Initialize Directories
define('EDD_BK_DIR', plugin_dir_path(EDD_BK));
define('EDD_BK_BASE', plugin_basename(EDD_BK));
define('EDD_BK_VENDOR_DIR', EDD_BK_DIR . 'vendor/');
define('EDD_BK_AUTOLOAD_FILE', EDD_BK_VENDOR_DIR . 'autoload.php');
define('EDD_BK_LANG_DIR', dirname(EDD_BK_BASE) . '/languages');
define('EDD_BK_CONFIG_DIR', EDD_BK_DIR . 'config/');
define('EDD_BK_INCLUDES_DIR', EDD_BK_DIR . 'includes/');
define('EDD_BK_VIEWS_DIR', EDD_BK_DIR . 'views/');
define('EDD_BK_ADMIN_DIR', EDD_BK_INCLUDES_DIR . 'admin/');
define('EDD_BK_PUBLIC_DIR', EDD_BK_INCLUDES_DIR . 'public/');
define('EDD_BK_DOWNLOADS_DIR', EDD_BK_INCLUDES_DIR . 'downloads/');
define('EDD_BK_BOOKINGS_DIR', EDD_BK_INCLUDES_DIR . 'bookings/');
define('EDD_BK_CUSTOMERS_DIR', EDD_BK_INCLUDES_DIR . 'customers/');
define('EDD_BK_EXCEPTIONS_DIR', EDD_BK_INCLUDES_DIR . 'exceptions/');
define('EDD_BK_WP_HELPERS_DIR', EDD_BK_INCLUDES_DIR . 'wp-helpers/');
define('EDD_BK_INTEGRATIONS_DIR', EDD_BK_DIR . 'integrations/');

// Set up the uploads directory
$uploadsDir = wp_upload_dir();
$eddBookingsUploadDir = array($uploadsDir['basedir'], 'edd-bookings');
$eddBookingsUploadUrl = array($uploadsDir['baseurl'], 'edd-bookings');
define('EDD_BK_UPLOADS_DIR', implode(DIRECTORY_SEPARATOR, $eddBookingsUploadDir));
define('EDD_BK_UPLOADS_URL', implode('/', $eddBookingsUploadUrl));
if (!file_exists(EDD_BK_UPLOADS_DIR)) {
    wp_mkdir_p(EDD_BK_UPLOADS_DIR);
}

// Initialize URLs
define('EDD_BK_PLUGIN_URL', plugin_dir_url(EDD_BK));
define('EDD_BK_ASSETS_URL', EDD_BK_PLUGIN_URL . 'assets/');
define('EDD_BK_CSS_URL', EDD_BK_ASSETS_URL . 'css/');
define('EDD_BK_JS_URL', EDD_BK_ASSETS_URL . 'js/');
define('EDD_BK_IMGS_URL', EDD_BK_ASSETS_URL . 'imgs/');
define('EDD_BK_FONTS_URL', EDD_BK_ASSETS_URL . 'fonts/');
define('EDD_BK_INTEGRATIONS_URL', EDD_BK_PLUGIN_URL . 'integrations/');

// For Debugging
define('EDD_BK_DEBUG', FALSE);

// Check minimum php version
if (version_compare(PHP_VERSION, EDD_BK_MIN_PHP_VERSION, '<')) {
    // load plugins.php file from WordPress if not loaded
    require_once(ABSPATH . 'wp-admin/includes/plugin.php');
    deactivate_plugins(__FILE__);
    wp_die(
        sprintf(
            _x('EDD Bookings requires PHP version %s or later.', 'Example: EDD Bookings requires PHP 5.3 or later', 'eddbk'),
            EDD_BK_MIN_PHP_VERSION
        ),
        __('EDD Bookings has been disabled', 'eddbk'),
        array(
            'back_link' => true
        )
    );
}

// Check if vendor dir and autoload file exist
if (is_dir(EDD_BK_VENDOR_DIR) && file_exists(EDD_BK_AUTOLOAD_FILE)) {
    // Load the autoloader file
    require EDD_BK_AUTOLOAD_FILE;
}
// This message should never be displayed to users. It only exists for the sake of development as a reminder that dependencies need to be installed.
else if (!class_exists('Composer\Autoload\ClassLoader')) {
    // load plugins.php file from WordPress if not loaded
    require_once(ABSPATH . 'wp-admin/includes/plugin.php');
    deactivate_plugins(__FILE__);
    wp_die(
        sprintf(
            '%1$s<br/>%2$s <code>composer install</code><br/>%3$s',
            __('Plugin dependencies are not loaded!', 'eddbk'),
            __('If you are using Composer with WordPress, make sure that the autoload.php file being loaded.', 'eddbk'),
            __('The EDD Bookings plugin will be deactivated.', 'eddbk')
        ),
        __('Plugin dependencies are not installed!', 'eddbk'),
        array(
            'back_link' => true
        )
    );
}

/**
 * Gets the plugin main class singleton instance.
 * 
 * @staticvar Aventura\Edd\Bookings\Plugin $instance The singleton instance
 * @return \Aventura\Edd\Bookings\Plugin The singleton instance.
 */
function eddBookings()
{
    /* @var $instance \Aventura\Edd\Bookings\Plugin */
    static $instance = null;
    // If null, instantiate
    if (is_null($instance)) {
        // Create the factory
        $defaultFactoryClass = 'Aventura\\Edd\\Bookings\\Factory';
        $filteredFactoryClass = apply_filters('edd_bk_main_factory_class', $defaultFactoryClass);
        $factoryClass = (is_subclass_of($filteredFactoryClass, $defaultFactoryClass))
                ? $filteredFactoryClass
                : $defaultFactoryClass;
        /* @var $factory \Aventura\Edd\Bookings\Factory */
        $factory = new $factoryClass();
        // Create the plugin
        $instance = $factory->create();
        // Set factory's parent plugin pointer
        $factory->setPlugin($instance);
        // Throw exception if the filtered factory class did not exist and the default had to be used
        if ($filteredFactoryClass !== $defaultFactoryClass && $factoryClass === $defaultFactoryClass) {
            $msg = sprintf('The %s class does not exist or is not a valid factory class. The default was used.', $filteredFactoryClass);
            throw new Exception($msg);
        }
    }
    return $instance;
}

// Activation/Deactivation hooks
register_activation_hook(__FILE__, array(eddBookings(), 'onActivate'));
register_deactivation_hook(__FILE__, array(eddBookings(), 'onDeactivate'));

// Load the integrations
eddBookings()->loadDirectory('integrations');

// Hook in the plugin - In actuality, the plugin is registering its hooks with the Hook Manager
eddBookings()->hook();
// This makes the Hook Manager register the saved hooks to WordPress
eddBookings()->getHookManager()->registerHooks();
