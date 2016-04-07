<?php


/**
 * @wordpress-plugin
 * Plugin Name: Easy Digital Downloads - Bookings
 * Plugin URL: http://eddbookings.com
 * Description: Adds a simple booking system to Easy Digital Downloads
 * Version: 1.0.3
 * Author: Jean Galea
 * Contributors: Miguel Muscat
 */
// If the file is called directly, or has already been called, abort
if (!defined('WPINC') || defined('EDD_BK')) {
    die;
}

// Plugin File Constant
define('EDD_BK', __FILE__);
// Plugin Version
define('EDD_BK_VERSION', '1.0.3');
// Plugin Name
define('EDD_BK_PLUGIN_NAME', 'EDD Bookings');
// Plugin Author
define('EDD_BK_PLUGIN_AUTHOR', 'Jean Galea');
// Parent Plugin Class name
define('EDD_BK_PARENT_PLUGIN_CLASS', 'Easy_Digital_Downloads');
// Parent Plugin Min version required
define('EDD_BK_PARENT_PLUGIN_MIN_VERSION', '2.3');
// Minimum WordPress version
define('EDD_BK_MIN_WP_VERSION', '4.0');
// Minimum PHP Version
define('EDD_BK_MIN_PHP_VERSION', '5.3.9');
// Database version number
define('EDD_BK_DB_VERSION', '1');
// Default text domain
define('EDD_BK_TEXT_DOMAIN', 'edd_bk');

// Initialize Directories
define('EDD_BK_DIR', plugin_dir_path(EDD_BK));
define('EDD_BK_BASE', plugin_basename(EDD_BK));
define('EDD_BK_LANG_DIR', EDD_BK_DIR . 'languages/');
define('EDD_BK_INCLUDES_DIR', EDD_BK_DIR . 'includes/');
define('EDD_BK_VIEWS_DIR', EDD_BK_DIR . 'views/');
define('EDD_BK_ADMIN_DIR', EDD_BK_INCLUDES_DIR . 'admin/');
define('EDD_BK_PUBLIC_DIR', EDD_BK_INCLUDES_DIR . 'public/');
define('EDD_BK_DOWNLOADS_DIR', EDD_BK_INCLUDES_DIR . 'downloads/');
define('EDD_BK_BOOKINGS_DIR', EDD_BK_INCLUDES_DIR . 'bookings/');
define('EDD_BK_CUSTOMERS_DIR', EDD_BK_INCLUDES_DIR . 'customers/');
define('EDD_BK_EXCEPTIONS_DIR', EDD_BK_INCLUDES_DIR . 'exceptions/');
define('EDD_BK_WP_HELPERS_DIR', EDD_BK_INCLUDES_DIR . 'wp-helpers/');

// Initialize URLs
define('EDD_BK_PLUGIN_URL', plugin_dir_url(EDD_BK));
define('EDD_BK_ASSETS_URL', EDD_BK_PLUGIN_URL . 'assets/');
define('EDD_BK_CSS_URL', EDD_BK_ASSETS_URL . 'css/');
define('EDD_BK_JS_URL', EDD_BK_ASSETS_URL . 'js/');
define('EDD_BK_IMGS_URL', EDD_BK_ASSETS_URL . 'imgs/');
define('EDD_BK_FONTS_URL', EDD_BK_ASSETS_URL . 'fonts/');

// For Debugging
define('EDD_BK_DEBUG', FALSE);

// Check minimum php version
if (version_compare(PHP_VERSION, EDD_BK_MIN_PHP_VERSION, '<')) {
    wp_die(sprintf('EDD Bookings requires PHP %s or later.', EDD_BK_MIN_PHP_VERSION));
}

// The autoload file
require EDD_BK_INCLUDES_DIR . 'autoload.php';

// Autoload the bookings library
eddBookingsAutoloader()->add('Aventura\\Diary', EDD_BK_INCLUDES_DIR);
// Autoload the plugin files
eddBookingsAutoloader()->add('Aventura\\Edd\\Bookings', EDD_BK_INCLUDES_DIR);

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
            $msg = sprintf('The %s class does not exist or is not a valid factory class. The default was used.',
                    $filteredFactoryClass);
            throw new Exception($msg);
        }
    }
    return $instance;
}

// Activation/Deactivation hooks
register_activation_hook(__FILE__, array(eddBookings(), 'onActivate'));
register_deactivation_hook(__FILE__, array(eddBookings(), 'onDeactivate'));

// eddBookings()->addIntegration('fes', new FesIntegration(eddBookings()));
// Hook in the plugin - In actuality, the plugin is registering its hooks with the Hook Manager
eddBookings()->hook();
// This makes the Hook Manager register the saved hooks to WordPress
eddBookings()->getHookManager()->registerHooks();
