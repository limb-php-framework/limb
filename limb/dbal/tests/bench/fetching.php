<?php

set_include_path(dirname(__FILE__) . '/../../../../');
define('LIMB_VAR_DIR', dirname(__FILE__) . '/../../../var/');

$mark = microtime(true);

require_once('limb/core/common.inc.php');
require_once('limb/dbal/common.inc.php');

echo "dbal common includes: " . (microtime(true) - $mark) . "\n";

$db = LIMB_VAR_DIR . '/benchdb';
if($native_db = sqlite_open($db))
{
  @sqlite_query($native_db, "DROP TABLE foo");
  sqlite_query($native_db, 'CREATE TABLE foo (id INTEGER PRIMARY KEY, bar VARCHAR(10))');
  for($i=0;$i<30;$i++)
    sqlite_query($native_db, "INSERT INTO foo VALUES (null, 'some value$i')");
}
else
  throw new Exception("Could not open sqlite db '$db'");

$mark = microtime(true);

for($i=0;$i<1000;$i++)
{
  $query = sqlite_query($native_db, 'SELECT bar FROM foo');
  while($entry = sqlite_fetch_array($query, SQLITE_ASSOC))
   $bar = $entry['bar'];
}

echo "native sqlite fetching: " . (microtime(true) - $mark) . "\n";

$conn = lmbDBAL :: newConnection('sqlite://localhost/' . $db);

$mark = microtime(true);

for($i=0;$i<1000;$i++)
{
  $rs = lmbDBAL :: fetch('SELECT bar FROM foo', $conn);
  foreach($rs as $record)
   $bar = $record['bar'];
}

echo "lmbDBAL :: fetch(), array access: " . (microtime(true) - $mark) . "\n";

$mark = microtime(true);

for($i=0;$i<1000;$i++)
{
  $rs = lmbDBAL :: fetch('SELECT bar FROM foo', $conn);
  foreach($rs as $record)
   $bar = $record->get('bar');
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

echo "lmbSqliteConnection :: newStatement(), getter: " . (microtime(true) - $mark) . "\n";

@unlink('/tmp/benchdb');
