<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

function DriverMssqlSetup($conn)
{
  DriverMssqlExec($conn, "if EXISTS(select name from sysobjects where name='founding_fathers')DROP TABLE founding_fathers");
  $sql = "CREATE TABLE founding_fathers (
            id int NOT null IDENTITY (4, 1),
            first varchar(50) NOT null default '',
            last varchar(50) NOT null default '',
            PRIMARY KEY (id)) ";
  DriverMssqlExec($conn, $sql);

  DriverMssqlExec($conn, "if EXISTS(select name from sysobjects where name='standard_types')DROP TABLE standard_types");
  $sql = "
        CREATE TABLE standard_types (
            id int NOT null IDENTITY(5, 1),
	          type_bit bit,
            type_smallint smallint,
            type_integer integer,
            type_boolean smallint,
            type_char char (30),
            type_varchar varchar (30),
            type_clob text,
            type_float float,
            type_double real,
            type_decimal decimal (30, 2),
            type_timestamp datetime,
            type_date smalldatetime,
            type_time datetime,
            type_blob binary,
            PRIMARY KEY (id)) ";
  DriverMssqlExec($conn, $sql);

  DriverMssqlExec($conn, 'DELETE FROM founding_fathers');
  DriverMssqlExec($conn, 'DELETE FROM standard_types');
  DriverMssqlExec($conn, 'UPDATE STATISTICS founding_fathers WITH FULLSCAN');
  DriverMssqlExec($conn, 'UPDATE STATISTICS standard_types WITH FULLSCAN');

  DriverMssqlExec($conn, "SET IDENTITY_INSERT founding_fathers ON");
  $inserts = array(
        "INSERT INTO founding_fathers (id, first, last) VALUES (1, 'George', 'Washington')",
        "INSERT INTO founding_fathers (id, first, last) VALUES (2, 'Alexander', 'Hamilton')",
        "INSERT INTO founding_fathers (id, first, last) VALUES (3, 'Benjamin', 'Franklin')"
    );

  foreach($inserts as $sql)
    DriverMssqlExec($conn, $sql);
}

function DriverMssqlExec($conn, $sql)
{
  $result = mssql_query($sql, $conn);
  if(!$result)
    throw new lmbDbException('MSSQL execute error happened: ' . mssql_get_last_message().". SQL: ".$sql);
}


