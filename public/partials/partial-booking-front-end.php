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
			'meta'				=> $meta,
		)
	);


	// Begin Output
	$slot_duration_unit = strtolower( $slot_duration_unit );
	$datepicker_units = array( 'minutes', 'hours', 'days' );

	
	?>
		<div id="edd-bk-datepicker-container">
			<div class="edd-bk-dp-skin">
				<div id="edd-bk-datepicker"></div>
				<button class="button edd-bk-datepicker-refresh" type="button">
					<i class="fa fa-refresh"></i> Refresh
				</button>
			</div>
			<input type="hidden" id="edd-bk-datepicker-value" name="edd_bk_date" value="" />
		</div>

		<div id="edd-bk-timepicker-container">
			<p id="edd-bk-timepicker-loading"><i class="fa fa-cog fa-spin"></i> Loading</p>
			<div id="edd-bk-timepicker">
				<?php if ( $slot_duration_unit == 'hours' || $slot_duration_unit == 'minutes' ) : ?>
					<p>
						<label><?php echo $duration_type === 'fixed'? 'Booking' : 'Start' ?> Time: </label>
						<select name="edd_bk_time"></select>
					</p>
				<?php endif; ?>
				<?php if ( $duration_type !== 'fixed' ) : ?>
					<?php
						$proper_min = floatval( $slot_duration ) * intval( $min_slots );
						$proper_max = floatval( $slot_duration ) * intval( $max_slots );
					?>
					<p>
						<label>Duration:</label>
						<input name="edd_bk_num_slots" type="number" step="<?= $slot_duration ?>" min="<?= $proper_min ?>" max="<?= $proper_max ?>" value="<?= $proper_min ?>" data-actual-max="<?= $proper_max ?>" required/>
						<?php echo $slot_duration_unit; ?>
					</p>
				<?php endif; ?>

				<p id="edd-bk-price">
					Price: <span></span>
				</p>
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

	<hr />
	<h4>Session</h4>

	<div style="zoom: 0.8"><?php var_dump($_SESSION); ?></div>
