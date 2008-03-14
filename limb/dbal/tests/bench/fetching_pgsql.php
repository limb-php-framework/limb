<?php

set_include_path(dirname(__FILE__) . '/../../../../');

$mark = microtime(true);

require_once('limb/core/common.inc.php');
require_once('limb/dbal/common.inc.php');

echo "dbal common includes: " . (microtime(true) - $mark) . "\n";
$conn_string = "host=sheep port=5432 dbname=test user=lamb password=bar";

$pgsql_db = pg_connect('host=localhost user=php password=1561611 dbname=medkrug');

pg_query($pgsql_db, 'CREATE TABLE foo (bar varchar(10))');
pg_query($pgsql_db, 'INSERT INTO foo VALUES (\'some value\')');


$mark = microtime(true);


for($i=0;$i<1000;$i++)
{
  $q = pg_query($pgsql_db, 'SELECT bar FROM foo');
  while($entry = pg_fetch_assoc($q))
   $bar = $entry['bar'];
}

echo "native pgsql fetching: " . (microtime(true) - $mark) . "\n";

$conn = lmbDBAL :: newConnection('pgsql://php:1561611@localhost/medkrug');

$mark = microtime(true);

for($i=0;$i<1000;$i++)
{
  $rs = lmbDBAL :: fetch('SELECT bar FROM foo', $conn);
  foreach($rs as $record)
   $bar = $record['bar'];
  $rs->freeQuery();
}

echo "lmbDBAL :: fetch(), array access: " . (microtime(true) - $mark) . "\n";

$mark = microtime(true);

for($i=0;$i<1000;$i++)
{
  $rs = lmbDBAL :: fetch('SELECT bar FROM foo', $conn);
  foreach($rs as $record)
   $bar = $record->get('bar');
  $rs->freeQuery();
}

echo "lmbDBAL :: fetch(), getter: " . (microtime(true) - $mark) . "\n";

$mark = microtime(true);

for($i=0;$i<1000;$i++)
{
  $stmt = $conn->newStatement('SELECT bar FROM foo');
  $rs = $stmt->getRecordSet();
  foreach($rs as $record)
   $bar = $record->get('bar');
}

echo "lmbPgsqlConnection :: newStatement(), getter: " . (microtime(true) - $mark) . "\n";


pg_query($pgsql_db, "drop table foo");

pg_close($pgsql_db);