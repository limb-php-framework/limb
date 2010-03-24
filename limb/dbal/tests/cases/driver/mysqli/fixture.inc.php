<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

error_reporting(E_ALL);

function DriverMysqliSetup($conn)
{
  DriverMysqliExec($conn, 'DROP TABLE IF EXISTS founding_fathers;');

  $sql = "CREATE TABLE founding_fathers (
            id int(11) NOT null auto_increment,
            first varchar(50) NOT null default '',
            last varchar(50) NOT null default '',
            PRIMARY KEY (id)) AUTO_INCREMENT=4 TYPE=InnoDB";
  DriverMysqliExec($conn, $sql);

  DriverMysqliExec($conn, 'TRUNCATE `founding_fathers`');
  $inserts = array(
        "INSERT INTO founding_fathers VALUES (1, 'George', 'Washington');",
        "INSERT INTO founding_fathers VALUES (2, 'Alexander', 'Hamilton');",
        "INSERT INTO founding_fathers VALUES (3, 'Benjamin', 'Franklin');"
  );

  foreach($inserts as $sql)
    DriverMysqliExec($conn, $sql);

  DriverMysqliExec($conn, 'DROP TABLE IF EXISTS indexes;');

  $sql = "CREATE TABLE `indexes` (
            `primary_column` int(11) NOT null auto_increment,
            `common_column` int(11) NOT null default 0,
            `unique_column` int(11) NOT null default 0,
            PRIMARY KEY (`primary_column`),
            KEY (`common_column`),
            UNIQUE `unique_column_named_index` (`unique_column`)
            ) AUTO_INCREMENT=0 TYPE=MEMORY";
  DriverMysqliExec($conn, $sql);

  DriverMysqliExec($conn, 'TRUNCATE `indexes`');


  DriverMysqliExec($conn, 'DROP TABLE IF EXISTS standard_types');

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
            PRIMARY KEY (id)) AUTO_INCREMENT=4 TYPE=InnoDB";
  DriverMysqliExec($conn, $sql);

  DriverMysqliExec($conn, 'TRUNCATE `standard_types`');

}

function DriverMysqliExec($conn, $sql)
{
//  var_dump($sql);
  $result = mysqli_query($conn, $sql);
  if(false === $result)
    throw new lmbDbException('MySQLi execute error happened: ', array('error' => mysqli_error($conn)));
}
