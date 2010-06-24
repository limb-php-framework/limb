<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

function DriverOciSetup($conn)
{
  $sql = "BEGIN EXECUTE IMMEDIATE 'DROP TABLE founding_fathers'; EXCEPTION WHEN OTHERS THEN NULL; END;";
  DriverOciExec($conn, $sql);

  $sql = "CREATE TABLE founding_fathers (
            id NUMBER PRIMARY KEY,
            first VARCHAR2(50) DEFAULT '' NOT NULL,
            last VARCHAR2(50) DEFAULT '' NOT NULL
            )";
  DriverOciExec($conn, $sql);

  $sql = "BEGIN EXECUTE IMMEDIATE 'DROP SEQUENCE founding_fathers_seq'; EXCEPTION WHEN OTHERS THEN NULL; END;";
  DriverOciExec($conn, $sql);

  $sql = "CREATE SEQUENCE founding_fathers_seq INCREMENT BY 1";
  DriverOciExec($conn, $sql);

  $sql = "CREATE OR REPLACE TRIGGER founding_fathers_trigger
BEFORE INSERT ON founding_fathers REFERENCING NEW AS NEW FOR EACH ROW
BEGIN SELECT founding_fathers_seq.nextval INTO :NEW.ID FROM dual; END;";
  DriverOciExec($conn, $sql);

  $sql = "BEGIN EXECUTE IMMEDIATE 'DROP SEQUENCE standard_types_seq'; EXCEPTION WHEN OTHERS THEN NULL; END;";
  DriverOciExec($conn, $sql);

  $sql = "CREATE SEQUENCE standard_types_seq INCREMENT BY 1";
  DriverOciExec($conn, $sql);

  $sql = "BEGIN EXECUTE IMMEDIATE 'DROP TABLE standard_types'; EXCEPTION WHEN OTHERS THEN NULL; END;";
  DriverOciExec($conn, $sql);

  $sql = "CREATE TABLE standard_types (
          id NUMBER PRIMARY KEY,
          type_smallint smallint,
          type_integer integer,
          type_double number(30, 2),
          type_decimal number(30, 2),
          type_float number(30, 2),
          type_char char(100),
          type_varchar varchar2(4000),
          type_boolean number,
          type_number number,
          type_raw raw(2000),
          type_date date,
          type_timestamp number,
          type_time date,
          type_blob blob,
          type_clob clob
      )";
  DriverOciExec($conn, $sql);

  $sql = "CREATE OR REPLACE TRIGGER standard_types_trigger
BEFORE INSERT ON standard_types REFERENCING NEW AS NEW FOR EACH ROW
BEGIN SELECT standard_types_seq.nextval INTO :NEW.ID FROM dual; END;";
  DriverOciExec($conn, $sql);

  $sql = 'TRUNCATE TABLE founding_fathers';
  DriverOciExec($conn, $sql);

  $sql = 'TRUNCATE TABLE standard_types';
  DriverOciExec($conn, $sql);

  $inserts = array(
        "INSERT INTO founding_fathers (first, last) VALUES ('George', 'Washington')",
        "INSERT INTO founding_fathers (first, last) VALUES ('Alexander', 'Hamilton')",
        "INSERT INTO founding_fathers (first, last) VALUES ('Benjamin', 'Franklin')"
  );

  foreach($inserts as $sql)
    DriverOciExec($conn, $sql);
}

function DriverOciExec($conn, $sql)
{
  $stmt = oci_parse($conn, $sql);
  if(!$stmt)
    throw new lmbDbException('OCI parse error happened', oci_error());

  if(!oci_execute($stmt))
    throw new lmbDbException('OCI execute error happened', oci_error($stmt));

  oci_free_statement($stmt);
}


