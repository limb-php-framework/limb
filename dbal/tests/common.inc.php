<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

class ConnectionTestStub
{
  function quoteIdentifier($id)
  {
    return "'$id'";//let's keep tests clean
  }
}

function loadTestingDbDump($dump_path)
{
  if(!file_exists($dump_path))
    die('"' . $dump_path . '" sql dump file not found!');

  $tables = array();
  $sql_array = file($dump_path);

  $toolkit = lmbToolkit :: instance();
  $conn = $toolkit->getDefaultDbConnection();

  foreach($sql_array as $sql)
  {
    if(!preg_match("|insert\s+?into\s+?([^\s]+)|i", $sql, $matches))
      continue;

    if(isset($tables[$matches[1]]))
      continue;

    $tables[$matches[1]] = $matches[1];

    $stmt = $conn->newStatement('DELETE FROM '. $matches[1]);
    $stmt->execute();
  }

  $GLOBALS['testing_db_tables'] = $tables;

  foreach($sql_array as $sql)
  {
    if(trim($sql))
    {
      $stmt = $conn->newStatement($sql);
      $stmt->execute();
    }
  }
}

function clearTestingDbTables()
{
  if(!isset($GLOBALS['testing_db_tables']))
    return;

  $toolkit = lmbToolkit :: instance();
  $conn = $toolkit->getDefaultDbConnection();

  foreach($GLOBALS['testing_db_tables'] as $table)
  {
    $stmt = $conn->newStatement('DELETE FROM '. $table);
    $stmt->execute();
  }

  $GLOBALS['testing_db_tables'] = array();
}

function parseTestingCriteria($criteria)
{
  $str = '';
  $criteria->appendStatementTo($str, $values);
  if($values)
    return strtr($str, $values);
  else
    return $str;
}

