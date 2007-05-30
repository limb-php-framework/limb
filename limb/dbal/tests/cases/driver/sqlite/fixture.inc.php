<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: fixture.inc.php 4994 2007-02-08 15:36:08Z pachanga $
 * @package    dbal
 */

function DriverSqliteSetup($conn)
{  
  if(DriverSqliteTableExists($conn, 'founding_fathers'))     
    DriverSqliteExec($conn, 'DROP TABLE founding_fathers');
  
  $sql = "CREATE TABLE founding_fathers (
            id INTEGER PRIMARY KEY,
            first VARCHAR,
            last VARCHAR)";
  DriverSqliteExec($conn, $sql);
  
  if(DriverSqliteTableExists($conn, 'standard_types'))
    DriverSqliteExec($conn, 'DROP TABLE standard_types');
  
  $sql = "CREATE TABLE standard_types (
            id INTEGER PRIMARY KEY,
            type_smallint smallint,
            type_integer integer,
            type_boolean smallint,
            type_char char (30),
            type_varchar varchar (30),
            type_clob text,
            type_float float,
            type_double double,
            type_decimal decimal (30, 2),
            type_timestamp datetime,
            type_date date,
            type_time time,
            type_blob blob)";
  DriverSqliteExec($conn, $sql);

  DriverSqliteExec($conn, 'DELETE founding_fathers');
  DriverSqliteExec($conn, 'DELETE standard_types');

  $inserts = array(
        "INSERT INTO founding_fathers VALUES (1, 'George', 'Washington');",
        "INSERT INTO founding_fathers VALUES (2, 'Alexander', 'Hamilton');",
        "INSERT INTO founding_fathers VALUES (3, 'Benjamin', 'Franklin');"
    );

  foreach($inserts as $sql)
    DriverSqliteExec($conn, $sql);
}

function DriverSqliteExec($conn, $sql)
{
  $result = sqlite_query($conn, $sql);
  if(!$result)
    throw new lmbDbException('SQLite error happened: ' . sqlite_error_string(sqlite_last_error($conn)));
  return $result;
}

function DriverSqliteTableExists($conn, $table) 
{
  $query = DriverSqliteExec($conn, "SELECT name FROM sqlite_master WHERE type='table'");
  if($tables = sqlite_fetch_array($query))  
    return in_array($table, $tables);
  else
    return false;
}

?>