<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/dbal/src/drivers/lmbDbTableInfo.class.php');
lmb_require('limb/dbal/src/drivers/pgsql/lmbPgsqlColumnInfo.class.php');

/**
 * class lmbPgsqlTableInfo.
 *
 * @package dbal
 * @version $Id: lmbPgsqlTableInfo.class.php 7486 2009-01-26 19:13:20Z pachanga $
 */
class lmbPgsqlTableInfo extends lmbDbTableInfo
{
  protected $database;

  protected $isExisting = false;
  protected $isColumnsLoaded = false;

  protected $oid;

  function __construct($database, $name, $isExisting = false, $oid = 1)
  {
    parent::__construct($name);
    $this->database = $database;
    $this->isExisting = $isExisting;
    $this->oid = $oid;
  }

  //Based on code from Creole
  function loadColumns()
  {

    if($this->isExisting && !$this->isColumnsLoaded)
    {
      $connection = $this->database->getConnection();

      $result = $connection->execute(sprintf("SELECT
                                                att.attname,
                                                att.atttypmod,
                                                att.atthasdef,
                                                att.attnotnull,
                                                def.adsrc,
                                                CASE WHEN att.attndims > 0 THEN 1 ELSE 0 END AS isarray,
                                                CASE
                                                  WHEN ty.typname = 'bpchar'
                                                    THEN 'char'
                                                  WHEN ty.typname = '_bpchar'
                                                    THEN '_char'
                                                  ELSE
                                                    ty.typname
                                                END AS typname,
                                                ty.typtype
                                                FROM pg_attribute att
                                                JOIN pg_type ty ON ty.oid=att.atttypid
                                                LEFT OUTER JOIN pg_attrdef def ON adrelid=att.attrelid AND adnum=att.attnum
                                                WHERE att.attrelid = %d AND att.attnum > 0
                                                AND att.attisdropped IS false
                                                ORDER BY att.attnum", $this->oid));

      while($row = pg_fetch_assoc($result))
      {
        if(((int) $row['isarray']) === 1)
        {
          $connection->_raiseError(sprintf("Array datatypes are not currently supported [%s.%s]", $this->name, $row['attname']));
        }

        $name = $row['attname'];
        if(strtolower($row['typtype']) == 'd')
        {
          //not supported yet

        }
        else
        {
          $type = $row['typname'];
          $arrLengthPrecision = $this->_processLengthPrecision($row['atttypmod'], $type);
          $size = $arrLengthPrecision['length'];
          $scale = $arrLengthPrecision['precision'];
          $boolHasDefault = $row['atthasdef'];
          $default = $row['adsrc'];
          $isNullable =(($row['attnotnull'] == 't') ?  false : true);
        }

        $isAutoIncrement = null;

        if(($boolHasDefault == 't') &&(strlen(trim($default)) > 0))
        {
          if(!preg_match('/^nextval\(/', $default))
          {
            $strDefault= preg_replace('/::[\W\D]*/', '', $default);
            $default = str_replace("'", '', $strDefault);
          }
          else
          {
            $isAutoIncrement = true;
            $default = null;
          }
        }
        else
        {
          $default = null;
        }

        $this->columns[$name] = new lmbPgsqlColumnInfo($this,
               $name, $type, $size, $scale, $isNullable, $default, $isAutoIncrement);
      }

      $this->isColumnsLoaded = true;
    }
  }

  function getDatabase()
  {
    return $this->database;
  }

  private function _processLengthPrecision($intTypmod, $strName)
  {
    $arrRetVal = array('length' => null, 'precision' => null);

    // Some datatypes don't have a Typmod
    if($intTypmod == -1)
    {
      return $arrRetVal;
    }

    // Numeric Datatype?
    if($strName == "numeric")
    {
      $intLen =($intTypmod - 4) >> 16;
      $intPrec =($intTypmod - 4) & 0xffff;
      $intLen = sprintf("%ld", $intLen);
      if($intPrec)
      {
        $intPrec = sprintf("%ld", $intPrec);
      }
      $arrRetVal['length'] = $intLen;
      $arrRetVal['precision'] = $intPrec;
    }
    elseif($strName == "time" || $strName == 'timetz'
        || $strName == "timestamp" || $strName == 'timestamptz'
        || $strName == 'interval' || $strName == 'bit')
    {
      $arrRetVal['length'] = sprintf("%ld", $intTypmod);
    }
    else
    {
      $arrRetVal['length'] = sprintf("%ld",($intTypmod - 4));
    }
    return $arrRetVal;
  }

  function loadIndexes()
  {
    lmb_require('limb/core/src/exception/lmbNotYetImplementedException.class.php');
    throw new lmbNotYetImplementedException();
  }
}


