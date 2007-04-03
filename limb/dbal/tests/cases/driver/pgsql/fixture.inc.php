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

function DriverPgsqlSetup($conn)
{
  $sql = "DROP TABLE founding_fathers CASCADE";
  DriverPgsqlExec($conn, $sql);

  $sql = '
      CREATE TABLE founding_fathers (
        "id" SERIAL,
        "first" varchar(50) NOT NULL default \'\',
        "last" varchar(50) NOT NULL default \'\',
        PRIMARY KEY  (id))';
  DriverPgsqlExec($conn, $sql);

  $sql = "DROP TABLE standard_types CASCADE";
  DriverPgsqlExec($conn, $sql);

  $sql = '
      CREATE TABLE standard_types (
          "id" SERIAL,
          "type_smallint" smallint,
          "type_integer" integer,
          "type_boolean" bool,
          "type_char" char(30),
          "type_varchar" varchar(30),
          "type_clob" text,
          "type_float" float,
          "type_double" double precision,
          "type_decimal" decimal (30, 2),
          "type_timestamp" timestamp,
          "type_date" date,
          "type_time" time,
          "type_blob" text,
          PRIMARY KEY (id))';
  DriverPgsqlExec($conn, $sql);

  DriverPgsqlExec($conn, 'TRUNCATE founding_fathers');
  DriverPgsqlExec($conn, 'TRUNCATE standard_types');

  $inserts = array(
      "INSERT INTO founding_fathers(first, last) VALUES ('George', 'Washington');",
      "INSERT INTO founding_fathers(first, last) VALUES ('Alexander', 'Hamilton');",
      "INSERT INTO founding_fathers(first, last) VALUES ('Benjamin', 'Franklin');"
  );

  foreach($inserts as $sql)
    DriverPgsqlExec($conn, $sql);
}

function DriverPgsqlExec($conn, $sql)
{
  $result = @pg_query($conn, $sql);
  if(!$result && stripos($sql, 'DROP') === false) //ignoring drop errors
    throw new lmbDbException('PgSQL execute error happened: ' . pg_last_error($conn));
}


?>