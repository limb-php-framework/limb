<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

function DriverLinterSetup($conn)
{
  $sql = 'DROP TABLE "founding_fathers" CASCADE;';
  DriverLinterExec($conn, $sql);

  $sql = '
      CREATE OR REPLACE TABLE "founding_fathers" (
        "id" INT NOT NULL AUTOINC,
        "first" varchar(50) NOT NULL default \'\',
        "last" varchar(50) NOT NULL default \'\',
        PRIMARY KEY  ("id"));';
  DriverLinterExec($conn, $sql);

  $sql = 'DROP TABLE "standard_types" CASCADE;';
  DriverLinterExec($conn, $sql);

  $sql = '
      CREATE OR REPLACE TABLE "standard_types" (
          "id" INT NOT NULL AUTOINC,
          "type_smallint" smallint,
          "type_integer" integer,
          "type_boolean" boolean,
          "type_char" char(30),
          "type_varchar" varchar(30),
          "type_clob" blob,
          "type_float" float,
          "type_double" double precision,
          "type_decimal" decimal (20, 2),
          "type_timestamp" date,
          "type_date" date,
          "type_time" date,
          "type_blob" blob,
          PRIMARY KEY ("id"));';
  DriverLinterExec($conn, $sql);

  DriverLinterExec($conn, 'TRUNCATE TABLE "founding_fathers";');
  DriverLinterExec($conn, 'TRUNCATE TABLE "standard_types";');

  $inserts = array(
      'INSERT INTO "founding_fathers" ("first", "last") VALUES (\'George\', \'Washington\');',
      'INSERT INTO "founding_fathers" ("first", "last") VALUES (\'Alexander\', \'Hamilton\');',
      'INSERT INTO "founding_fathers" ("first", "last") VALUES (\'Benjamin\', \'Franklin\');'
  );

  foreach($inserts as $sql)
    DriverLinterExec($conn, $sql);
}

function DriverLinterExec($conn, $sql)
{
  $result = @linter_exec_direct($conn, $sql);
  if($result < 0 && stripos($sql, 'DROP') === false)
  { //ignoring drop errors
    _raiseError($result, $conn, array('sql' => $sql));
    throw new lmbDbException('Linter execute error happened: ' . $sql . linter_last_error($conn));
  }
}


  function _raiseError($code, $conn_id = null, $args=array())
  {
    if($code == LPE_INVALID_CONNECT)
    {
      $err_code = -1;
      $err_message = "Invalid connect";
    }
    elseif($code == LPE_LINTER_ERROR)
    {
      $lin_err = linter_last_error($conn_id, LINTER_ERROR);
      $sys_err = linter_last_error($conn_id, SYSTEM_ERROR);
      $err_message = linter_error_msg($conn_id);
      $err_code = $lin_err;
      var_dump($err_code);
      var_dump($args['sql']);
      if ($err_code <= 2 && $err_code > 0) return false;
      if ($err_code >= 2000 && $err_code < 3000)
      {
        $err_row = $sys_err & 0xFFFF;
        $err_pos = $sys_err >> 16;
        $err_message .= sprintf(" at row %d, position %d", $err_row, $err_pos);
      }
      else
        $err_message .= sprintf(", system error %d", $sys_err);
    }
    else
    {
      $err_message = sprintf("Linter extension error %d", $code);
      $err_code = $code;
    }
    throw new lmbException("Database error: No:" . $err_code.". Description: ".$err_message);
    return true;
  }



