<?php

	// Check if booking is enabled. If not, stop.
	if ( ! get_post_meta( get_the_ID(), 'edd_bk_enabled', TRUE ) ) return;

	// Get the meta data
	$post_id = get_the_ID();

	// Get the meta data for this post
	$meta = EDD_BK_Commons::meta_fields( $post_id );
	// Extract the meta fields into variables
	extract($meta);

	wp_localize_script(
		'edd-bk-download-public',
		'edd_bk',
		array(
			'post_id'			=> $post_id,
			'ajaxurl'			=> admin_url( 'admin-ajax.php' ),
			'fill'				=> $meta['availability_fill'],
			'availabilities'	=> $meta['availability'],
		)
	);


	// Begin Output ?>

	<div id="edd-bk-datepicker-container">
		<div class="edd-bk-dp-skin">
			<div id="edd-bk-datepicker"></div>

			<button class="button edd-bk-datepicker-refresh" type="button">
				<i class="fa fa-refresh"></i> Refresh
			</button>
		</div>
	</div>

	<div id="edd-bk-timepicker-container">
		<p id="edd-bk-timepicker-loading"><i class="fa fa-cog fa-spin"></i> Loading</p>
		<div id="edd-bk-timepicker">
			<label>Pick a time: </label>
			<select></select>
		</div>
	</div>


	<?php if ( !defined( 'EDD_BK_DEBUG' ) || ! EDD_BK_DEBUG ) return; ?>

	<hr/>

	<h4>Time Picker Written Logic</h4>

	<p id="edd-bk-timepicker"></p>
	<?php if ( $duration_type == 'fixed' ) : ?>
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

	<hr />
	<h4>This Download's Booking Meta Data</h4>

	<div style="zoom: 0.8"><?php var_dump($meta); ?></div>

