<?php

$id = get_the_ID();
$enabled = get_post_meta( $id, 'edd_bk_enabled', TRUE );

if ( $enabled ) : ?>
	<p>You can book</p>
<?php endif;