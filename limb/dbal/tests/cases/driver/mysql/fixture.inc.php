<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
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
  DriverMysqlExec($conn, 'TRUNCATE founding_fathers');
  $inserts = array(
        "INSERT INTO founding_fathers VALUES (1, 'George', 'Washington');",
        "INSERT INTO founding_fathers VALUES (2, 'Alexander', 'Hamilton');",
        "INSERT INTO founding_fathers VALUES (3, 'Benjamin', 'Franklin');"
    );

  DriverMysqlExec($conn, 'DROP TABLE IF EXISTS `indexes`');
  $sql = "CREATE TABLE `indexes` (
            `primary_column` int(11) NOT null auto_increment,
            `common_column` int(11) NOT null default 0,
            `unique_column` int(11) NOT null default 0,
            PRIMARY KEY (`primary_column`),
            KEY (`common_column`),
            UNIQUE `unique_column_named_index` (`unique_column`)
            ) AUTO_INCREMENT=0 TYPE=MEMORY";
  DriverMysqlExec($conn, $sql);

  DriverMysqlExec($conn, 'TRUNCATE `indexes`');

  DriverMysqlExec($conn, 'DROP TABLE IF EXISTS standard_types');
  $sql = "
        CREATE TABLE standard_types (
            id int(11) NOT null auto_increment,
	    type_bit bit,
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
  DriverMysqlExec($conn, 'TRUNCATE standard_types');

  foreach($inserts as $sql)
    DriverMysqlExec($conn, $sql);
}

function DriverMysqlExec($conn, $sql)
{
  $result = mysql_query($sql, $conn);
  if(!$result)
    throw new lmbDbException('MySQL execute error happened: ' . mysql_error($conn));
}


