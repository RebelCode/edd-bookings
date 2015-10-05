<?php
/**
 * View file for contextual help shown on the admin "Help" tab.
 *
 * @since 1.0.0
 * @version 1.0.0
 * @package EDD_Booking\Views
 */
?>

<p>
	<strong><?php _e( 'Date and Time Formats'); ?></strong> - 
	For date formats use <code>dd/mm/yyyy</code>. For time formats use <code>24-hour</code> format.
	All dates and times use your WordPress
	<a href="<?php echo admin_url('options-general.php#timezone_string'); ?>" target="edd-booking-timezone-settings">
		timezone settings
	</a>
</p>