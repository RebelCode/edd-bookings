<?php

// Check if booking is enabled. If not, stop.
if ( ! get_post_meta( get_the_ID(), 'edd_bk_enabled', TRUE ) ) return;

// Get the meta data
$id = get_the_ID();

// Get the names of the meta fields from the Metaboxes class
require_once EDD_BK_ADMIN_DIR.'class-edd-bk-metaboxes.php';
$meta_fields = EDD_BK_Admin_Metaboxes::meta_fields();
$meta = array();
// Generate a new meta array, containing the actual meta values from the database
foreach ($meta_fields as $i => $field) {
	$meta[$field] = get_post_meta( $id, 'edd_bk_'.$field, true );
}
// Extract the meta fields into variables
extract($meta);

wp_localize_script(
	'edd-bk-download-public',
	'edd_bk',
	array(
		'availabilities' => $meta['availability']
	)
);


// Begin Output ?>
<h2>Book your dates!</h2>

<div id="edd-bk-datepicker-container">
	<div class="edd-bk-dp-skin">
		<div id="edd-bk-datepicker"></div>
	</div>
</div>

<hr/>


<h4>Notes re. plugin logic</h4>

<?php if ( $duration_type == 'fixed' ) : ?>
		<p>You can book <?php echo $slot_duration . ' '. __( str_sing_plur( $slot_duration, $slot_duration_unit ) ); ?></p>
<?php else: ?>
		<p>
			You can book
			<?php
				$proper_min = floatval( $slot_duration ) * intval( $min_slots );
				$proper_max = floatval( $slot_duration ) * intval( $max_slots );

				$list = array();
				for ( $i = $proper_min; $i <= $proper_max; $i += $slot_duration ) {
					$list[] = $i;
				}

				$count = count( $list );
				if ( $count > 8 ) {
					array_splice( $list, 4, $count - 8, array( '...' ) );
				}
				$list = array_merge( array_slice( $list, 0, -1 ), array( 'or ' . end( $list ) ) );
				$list = implode( ', ', $list );

				echo $list;
				echo ' ' . __( str_plur( $slot_duration_unit ) );
			?>
		</p>
<?php endif; ?> 


<hr/>
<h4>Quick docs</h4>

<p>
Dates and times can be handled separately.
In the availability table, choosing only dates, days, weeks or months, only creates dates available for picking from the date picker.
Time availabilities must also be chosen, to create the time picking options.
</p>

<p>
It would seem that the jQuery UI Datepicker has an option <code>beforeShowDay</code>.
This option can be set on initialization to a function, that returns <code>true</code> or <code>false</code> for each date, signifiying if the date is available to be picked or not.
Server validation must also be carried out, to prevent JS-able, or hackers, from booking incorrect dates.
</p>

<hr />
<h4>This Download's Booking Data</h4>

<div style="zoom: 0.8"><?php var_dump($meta); ?></div>

