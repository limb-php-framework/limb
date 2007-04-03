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

function DriverMysqlSetup($conn)
{
  DriverMysqlExec($conn, 'DROP TABLE IF EXISTS founding_fathers');
  $sql = "CREATE TABLE founding_fathers (
            id int(11) NOT null auto_increment,
            first varchar(50) NOT null default '',
            last varchar(50) NOT null default '',
            PRIMARY KEY (id)) AUTO_INCREMENT=4 TYPE=InnoDB";
  DriverMysqlExec($conn, $sql);

  DriverMysqlExec($conn, 'DROP TABLE IF EXISTS standard_types');
  $sql = "
        CREATE TABLE standard_types (
            id int(11) NOT null auto_increment,
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
            type_blob blob,
            PRIMARY KEY (id)) AUTO_INCREMENT=4";
  DriverMysqlExec($conn, $sql);

  DriverMysqlExec($conn, 'TRUNCATE founding_fathers');
  DriverMysqlExec($conn, 'TRUNCATE standard_types');

  $inserts = array(
        "INSERT INTO founding_fathers VALUES (1, 'George', 'Washington');",
        "INSERT INTO founding_fathers VALUES (2, 'Alexander', 'Hamilton');",
        "INSERT INTO founding_fathers VALUES (3, 'Benjamin', 'Franklin');"
    );

  foreach($inserts as $sql)
    DriverMysqlExec($conn, $sql);
}

function DriverMysqlExec($conn, $sql)
{
  $result = mysql_query($sql, $conn);
  if(!$result)
    throw new lmbDbException('MySQL execute error happened: ' . mysql_error($conn));
}

?>