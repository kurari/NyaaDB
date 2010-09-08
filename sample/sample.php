<?php
require_once 'db/db.class.php';

$dir = dirname(__FILE__);

$handler = NyaaDB::factory("sqlite:localhost/$dir/sample.db");

$hadnler->query('CREATE TABLE sample (id integer,name varchar(128))');

$sth = $hadnler->prepare('INSERT INTO sample (id,name) VALUES (:id,:name)');
$sth->bindParam('id', 1, 'int');
$sth->bindParam('name', "hajime", 'str');
$sth->execute( );

$id = $sth->getLastId( );

$sth = $hadnler->prepare('SELECT * FROM sample WHERE id=:id;');
$sth->bindParam('id', 1, 'int');
$sth->execute( );

$row = $sth->fetch( );
echo $row['name'];
?>
