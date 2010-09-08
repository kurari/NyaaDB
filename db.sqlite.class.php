<?php
/**
 * @package DB
 */
class NyaaDBSqlite extends NyaaDB {
	var $con;
	var $db;

	function __construct( $str ){
		parent::__construct( );
		if( preg_match( '/^([^:]+):([^\/]+)\/(.*)/', $str, $m ) ){
			$type = $m[1];
			$host = $m[2];
			$db = $this->db = $m[3];
		}
		$this->con = sqlite_open( $db );
	}

	function backup( ){
		return $this->db;
	}
	function restore( $file ){
		copy($file, $this->db);
	}

	function getTableList( ){
		$tables = array( );
		$sth = $this->query(
			"SELECT name FROM sqlite_master WHERE type='table' 
			UNION ALL 
			SELECT name FROM sqlite_temp_master WHERE type='table' ORDER BY name;"
		);
		foreach( $sth->fetchAll( ) as $row ){
			$tables[] = $row['name'];
		}
		return $tables;
	}

	function query( $sql ){
		$Sth = new NyaaDBState( $this, $sql );
		$Sth->execute( );
		return $Sth;
	}

	function queryRow( $sql ){
		return sqlite_query( $this->con, $sql );
	}

	function executeFile( $file ){
		$data = file_get_contents( $file );
		foreach( explode( ';', $data ) as $sql ){
			$this->query( $sql );
		}
	}

	function escape( $param ){
		if(is_array($param) ){
			$this->error("can't escape:");
			var_dump($param);
		}
		$param = sqlite_escape_string( $param );
		$param = str_replace('"', "'", $param);
		return $param;
	}

	function fetchAll( $res ){
		return sqlite_fetch_all( $res, SQLITE_ASSOC );
	}

	function fetch( $res ){
		return sqlite_fetch_array( $res, SQLITE_ASSOC );
	}

	function getLastId( ){
		$sth = $this->query('SELECT last_insert_rowid( ) AS id');
		$data = $sth->fetchAll( );
		return $data[0]['id'];
	}

	function begin( ){
		$this->query('begin;');
	}

	function commit( ){
		$this->query('commit;');
	}

	function affected( $res ){
		return sqlite_num_rows( $res );
	}
}


?>
