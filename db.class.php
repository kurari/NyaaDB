<?php
require_once 'object/object.class.php';
require_once 'db/state.class.php';

/**
 * @package DB
 */
class NyaaDB extends NyaaObject {
	// sqlite:localhost/db
	// mysql:localhost/test
	function factory( $str ){
		if( preg_match( '/^([^:]+):(.*)/', $str, $m ) ){
			switch( $m[1] ){
			case "sqlite":
				require_once dirname(__FILE__)."/db.sqlite.class.php";
				$class = 'NyaaDBSqlite';
				break;
			}
		}

		return new $class( $str );
	}

	function prepare( $sql ){
		return new NyaaDBState( $this, $sql );
	}
}


?>
