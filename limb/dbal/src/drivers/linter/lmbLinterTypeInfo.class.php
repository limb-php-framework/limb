<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/dbal/src/drivers/lmbDbTypeInfo.class.php');

/**
 * class lmbLinterTypeInfo.
 *
 * @package dbal
 * @version $Id: $
 */
class lmbLinterTypeInfo extends lmbDbTypeInfo
{
  function getNativeToColumnTypeMapping()
  {
    return array(
      'SMALLINT' => LIMB_DB_TYPE_SMALLINT,
      'INTEGER' => LIMB_DB_TYPE_INTEGER,
      'INT' => LIMB_DB_TYPE_INTEGER,
      'BIGINT' => LIMB_DB_TYPE_DECIMAL,
      'BYTE' => LIMB_DB_TYPE_CHAR,
      'NCHAR_VARYING' => LIMB_DB_TYPE_VARCHAR,
      'REAL' => LIMB_DB_TYPE_FLOAT,
      'FLOAT' => LIMB_DB_TYPE_FLOAT,
      'cash' => LIMB_DB_TYPE_FLOAT,
      'money' => LIMB_DB_TYPE_FLOAT,
      'DECIMAL' => LIMB_DB_TYPE_DECIMAL,
      'NUMERIC' => LIMB_DB_TYPE_DECIMAL,
      'DOUBLE' => LIMB_DB_TYPE_DOUBLE,
      'CHAR' => LIMB_DB_TYPE_CHAR,
      'CHARACTER' => LIMB_DB_TYPE_CHAR,
      'VARCHAR' => LIMB_DB_TYPE_VARCHAR,
      'DATE' => LIMB_DB_TYPE_DATE,
      'BLOB' => LIMB_DB_TYPE_BLOB,
      'BOOLEAN' => LIMB_DB_TYPE_BOOLEAN
   );
  }
}

