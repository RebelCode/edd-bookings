<?php

// Shortcut for DIRECTORY SEPARATOR constant
if (!defined('DS')) define('DS', DIRECTORY_SEPARATOR);
// Define path root
define( 'AVENTURA_BOOKINGS_ROOT_PATH', dirname( __FILE__ ) . DS );
// Load autoloader - the irony
require AVENTURA_BOOKINGS_ROOT_PATH . 'Autoloader.php';

// Instantiate the autoloader singleton, and register the ROOT PATH
Aventura_Bookings_Autoloader::getInstance()->registerDirectory( AVENTURA_BOOKINGS_ROOT_PATH );
