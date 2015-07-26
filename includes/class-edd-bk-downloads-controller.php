<?php

class EDD_BK_Downloads_Controller {
	
	public static function get( $id ) {
		$meta = get_post_meta( $id, 'edd_bk', TRUE );
		return new EDD_BK_Download($meta);
	}

}