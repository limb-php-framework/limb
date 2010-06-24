<?php
set_include_path(dirname(__FILE__) . '/../../../../');

$test = null;
if(isset($argv[1]))
  $test = $argv[1];

$mark = microtime(true);
$counter = 0;

require_once('limb/core/common.inc.php');

lmb_env_set('LIMB_VAR_DIR', dirname(__FILE__) . '/../../../var/');
lmb_env_set('LIMB_CACHE_DB_META_IN_FILE', true);

require_once('limb/active_record/common.inc.php');

echo "active record common includes: " . (microtime(true) - $mark) . "\n";

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

if(++$counter == $test || $test == null)
{
  $mark = microtime(true);
  for($i=0;$i<1000;$i++)
  {
    $query = sqlite_query($native_db, 'SELECT bar FROM foo');
    while($entry = sqlite_fetch_array($query, SQLITE_ASSOC))
      $bar = $entry['bar'];
  }
  echo "$counter) native sqlite fetching: " . (microtime(true) - $mark) . "\n";
}

$conn = lmbDBAL :: newConnection('sqlite://localhost/' . $db);

if(++$counter == $test || $test == null)
{
  $mark = microtime(true);
  for($i=0;$i<1000;$i++)
  {
    $rs = lmbDBAL :: fetch('SELECT bar FROM foo', $conn);
    foreach($rs as $record)
      $bar = $record->get('bar');
  }
  echo "$counter) lmbDBAL :: fetch(), getter: " . (microtime(true) - $mark) . "\n";
}

if(++$counter == $test || $test == null)
{
  $mark = microtime(true);
  $rs = lmbDBAL :: fetch('SELECT bar FROM foo', $conn);
  for($i=0;$i<1000;$i++)
  {
    foreach($rs as $record)
      $bar = $record->get('bar');
  }
  echo "$counter) lmbDBAL :: fetch()(out of loop), getter: " . (microtime(true) - $mark) . "\n";
}

class Foo extends lmbActiveRecord
{
  protected $_db_table_name = 'foo';
}

lmbActiveRecord :: setDefaultDSN('sqlite://localhost/' . $db); 

if(++$counter == $test || $test == null)
{
  $mark = microtime(true);
  for($i=0;$i<1000;$i++)
  {
    $rs = lmbActiveRecord :: find('Foo');
    foreach($rs as $obj)
      $foo = $obj->get('bar');
  }
  echo "$counter) lmbActiveRecord fetching, getter: " . (microtime(true) - $mark) . "\n";
}


if(++$counter == $test || $test == null)
{
  $mark = microtime(true);
  for($i=0;$i<1000;$i++)
  {
    $rs = lmbActiveRecord :: find('Foo', array('proxy' => true));
    foreach($rs as $obj)
      $foo = $obj->get('bar');
  }
  echo "$counter) lmbActiveRecord fetching(proxied), getter: " . (microtime(true) - $mark) . "\n";
}

if(++$counter == $test || $test == null)
{
  $mark = microtime(true);
  $rs = lmbActiveRecord :: find('Foo');
  for($i=0;$i<1000;$i++)
  {
    foreach($rs as $obj)
      $foo = $obj->get('bar');
  }
  echo "$counter) lmbActiveRecord fetching(out of loop), getter: " . (microtime(true) - $mark) . "\n";
}

if(++$counter == $test || $test == null)
{
  $mark = microtime(true);
  $rs = lmbActiveRecord :: find('Foo', array('proxy' => true));
  for($i=0;$i<1000;$i++)
  {
    foreach($rs as $obj)
      $foo = $obj->get('bar');
  }
  echo "$counter) lmbActiveRecord fetching(proxied, out of loop), getter: " . (microtime(true) - $mark) . "\n";
}

if(++$counter == $test || $test == null)
{
  $mark = microtime(true);
  for($i=0;$i<1000;$i++)
  {
    $rs = lmbActiveRecord :: find('Foo');
    foreach($rs as $obj)
      $foo = $obj->bar;
  }
  echo "$counter) lmbActiveRecord fetching, attr access: " . (microtime(true) - $mark) . "\n";
}

if(++$counter == $test || $test == null)
{
  $mark = microtime(true);
  for($i=0;$i<1000;$i++)
  {
    $rs = lmbActiveRecord :: find('Foo', array('proxy' => true));
    foreach($rs as $obj)
      $foo = $obj->bar;
  }
  echo "$counter) lmbActiveRecord fetching(proxied), attr access: " . (microtime(true) - $mark) . "\n";
}

if(++$counter == $test || $test == null)
{
  $mark = microtime(true);
  $rs = lmbActiveRecord :: find('Foo');
  for($i=0;$i<1000;$i++)
  {
    foreach($rs as $obj)
      $foo = $obj->bar;
  }
  echo "$counter) lmbActiveRecord fetching(out of loop), attr access: " . (microtime(true) - $mark) . "\n";
}

if(++$counter == $test || $test == null)
{
  $mark = microtime(true);
  for($i=0;$i<1000;$i++)
  {
    $rs = Foo :: find();
    foreach($rs as $obj)
      $foo = $obj->get('bar');
  }
  echo "$counter) Foo fetching, getter: " . (microtime(true) - $mark) . "\n";
}

@unlink($db);

