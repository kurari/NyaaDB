<?php
/**
 * @package DB
 */

class NyaaIterator implements Iterator
{
	private $_vars = array();
	private $_i = 0;
	public function __construct( $array )
	{
		$this->_vars = $array;
	}

	public function rewind( )
	{
		$this->_i = 0;
	}
	public function current()
	{
		$k = array_keys($this->_vars);
		$var = $this->_vars[$k[$this->_i]];
		return $var;
	}

	public function next()
	{
		$k = array_keys($this->_vars);
		if( isset($k[++$this->_i])) {
			$var = $this->_vars[$k[$this->_i]];
			return $var;
		}else{
			return false;
		}
	}

	public function valid( )
	{
		$k = array_keys($this->_vars);
		$var = isset($k[$this->_i]);
		return $var;
	}

	public function key( )
	{
		$k = array_keys($this->_vars);
		$var = $k[$this->_i];
		return $var;
	}

}
class NyaaDBState extends NyaaStore implements IteratorAggregate{
	var $DAO;
	var $res;
	var $bind = array( );

	function getIterator( )
	{
		return new NyaaIterator($this->DAO->fetchAll( $this->res ));
	}

	function __construct( $DAO, $sql ){
		parent::__construct( );
		$this->DAO = $DAO;
		$this->sql = $sql;
	}

	function bindParams( $opt ){
		if(isset($opt) && is_array($opt)) foreach( $opt as $k=>$v ){
			$this->bindParam($k, $v);
		}
	}

	function bindParam( $key, $value, $type = "str" ){
		if($type == "array"){
			$data = array();
			foreach($value as $v){
				$data[] =sprintf('"%s"', $this->DAO->escape( $v ) );
			}
			$this->set( $key, implode(',', $data) );
		}elseif($type == "int"){
			$this->set( $key, sprintf('%s', $this->DAO->escape( $value ) ));
		}else{
			$this->set( $key, sprintf('"%s"', $this->DAO->escape( $value ) ));
		}
	}

	function getParam( $key ){
		if(!$this->get($key)){
			return '""';
		}else{
			return $this->get($key);
		}
	}

	function getSql( ){
		return preg_replace( '/:([^\s,\)\;]+)/e', '$this->getParam("\1")', $this->sql );
	}

	function execute(  $opt = array( ) ){
		$this->bindParams( $opt );
		$sql = $this->getSql( );
		$this->notice($sql);
		$this->res = $this->DAO->queryRow( $sql );
	}

	function fetchAll( ){
		return $this->DAO->fetchAll( $this->res );
	}
	function fetch( ){
		return $this->DAO->fetch( $this->res );
	}

	function getLastId( ){
		return $this->DAO->getLastId( $this->res );
	}
	
	function affected( ){
		return $this->DAO->affected( $this->res );
	}
}
?>
