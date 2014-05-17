<?php


function edd_bk_calendar( $download_id ) {
	echo "<p><b>BOLD!!!</b></p>";
}
add_action( 'edd_after_download_content', 'edd_bk_calendar' );
