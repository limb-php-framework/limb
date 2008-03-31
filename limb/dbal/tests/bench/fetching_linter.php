<?php

set_include_path(dirname(__FILE__) . '/../../../../');

$mark = microtime(true);

require_once('limb/core/common.inc.php');
require_once('limb/dbal/common.inc.php');

echo "dbal common includes: " . (microtime(true) - $mark) . "\n";

$linter_db = linter_open_connect('SYSTEM', 'MANAGER', 'Demo', TM_AUTOCOMMIT);
$cur = linter_open_cursor($linter_db);

linter_exec_direct($cur, 'CREATE OR REPLACE TABLE "foo" ("bar" varchar(10));');
linter_exec_direct($cur, 'INSERT INTO "foo" VALUES (\'some value\');');

$mark = microtime(true);


for($i=0;$i<1000;$i++)
{
  linter_exec_direct($cur, 'SELECT "bar" FROM "foo";');
  while(is_array($entry = linter_fetch_array($cur)))
   $bar = $entry['bar'];
}

echo "native linter fetching: " . (microtime(true) - $mark) . "\n";

$conn = lmbDBAL :: newConnection('linter://SYSTEM:MANAGER@localhost/Demo');

$mark = microtime(true);

for($i=0;$i<1000;$i++)
{
  $rs = lmbDBAL :: fetch('SELECT "bar" FROM "foo";', $conn);
  foreach($rs as $record)
   $bar = $record['bar'];
  $rs->freeQuery();
}

echo "lmbDBAL :: fetch(), array access: " . (microtime(true) - $mark) . "\n";

$mark = microtime(true);

for($i=0;$i<1000;$i++)
{
  $rs = lmbDBAL :: fetch('SELECT "bar" FROM "foo";', $conn);
  foreach($rs as $record)
   $bar = $record->get('bar');
  $rs->freeQuery();
}

echo "lmbDBAL :: fetch(), getter: " . (microtime(true) - $mark) . "\n";

$mark = microtime(true);

for($i=0;$i<1000;$i++)
{
  $stmt = $conn->newStatement('SELECT "bar" FROM "foo";');
  $rs = $stmt->getRecordSet();
  foreach($rs as $record)
   $bar = $record->get('bar');
}

echo "lmbLinterConnection :: newStatement(), getter: " . (microtime(true) - $mark) . "\n";

linter_close_connect($linter_db);
