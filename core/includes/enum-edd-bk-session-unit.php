<?php

abstract class EDD_BK_Session_Unit {

	const MINUTES	= 'minutes';
	const HOURS		= 'hours';
	const DAYS		= 'days';
	const WEEKS		= 'weeks';

	public static function add_plural_brackets( $unit ) {
		return substr( $unit, 0, -1 ) . '(s)';
	}

}
