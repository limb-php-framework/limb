<?php

set_include_path(dirname(__FILE__) . '/../../../../');

$mark = microtime(true);

require_once('limb/core/common.inc.php');
require_once('limb/active_record/common.inc.php');

echo "active record common includes: " . (microtime(true) - $mark) . "\n";

if($native_db = sqlite_open('/tmp/benchdb'))
{
  sqlite_query($native_db, 'CREATE TABLE foo (bar varchar(10))');
  sqlite_query($native_db, "INSERT INTO foo VALUES ('some value')");
}

$mark = microtime(true);

for($i=0;$i<1000;$i++)
{
  $query = sqlite_query($native_db, 'SELECT bar FROM foo');
  while($entry = sqlite_fetch_array($query, SQLITE_ASSOC))
   $bar = $entry['bar'];
}

echo "native sqlite fetching: " . (microtime(true) - $mark) . "\n";

$conn = lmbDBAL :: newConnection('sqlite://localhost//tmp/benchdb');
$mark = microtime(true);

for($i=0;$i<1000;$i++)
{
  $rs = lmbDBAL :: fetch('SELECT bar FROM foo', $conn);
  foreach($rs as $record)
   $bar = $record->get('bar');
}

echo "lmbDBAL :: fetch(), getter: " . (microtime(true) - $mark) . "\n";

class Foo extends lmbActiveRecord{}

lmbActiveRecord :: setDefaultDSN('sqlite://localhost//tmp/benchdb'); 

$mark = microtime(true);

for($i=0;$i<1000;$i++)
{
  $rs = lmbActiveRecord :: find('Foo');
  foreach($rs as $obj)
    $foo = $obj->get('foo');
}

echo "lmbActiveRecord fetching, getter: " . (microtime(true) - $mark) . "\n";

$mark = microtime(true);

for($i=0;$i<1000;$i++)
{
  $rs = lmbActiveRecord :: find('Foo');
  foreach($rs as $obj)
    $foo = $obj->foo;
}

echo "lmbActiveRecord fetching, attr access: " . (microtime(true) - $mark) . "\n";

$mark = microtime(true);

for($i=0;$i<1000;$i++)
{
  $rs = Foo :: find();
  foreach($rs as $obj)
    $foo = $obj->get('foo');
}

echo "Foo fetching, getter: " . (microtime(true) - $mark) . "\n";

unlink('/tmp/benchdb');
