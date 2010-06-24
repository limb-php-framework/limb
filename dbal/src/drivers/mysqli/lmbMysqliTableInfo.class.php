<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/dbal/src/drivers/lmbDbTableInfo.class.php');
lmb_require('limb/dbal/src/drivers/lmbDbIndexInfo.class.php');
lmb_require('limb/dbal/src/drivers/mysqli/lmbMysqliColumnInfo.class.php');
lmb_require('limb/dbal/src/drivers/mysqli/lmbMysqliIndexInfo.class.php');

/**
 * class lmbMysqliTableInfo.
 *
 * @package dbal
 * @version $Id: lmbMysqliTableInfo.class.php 6243 2007-08-29 11:53:10Z pachanga $
 */
class lmbMysqliTableInfo extends lmbDbTableInfo
{
  protected $isExisting = false;
  protected $isColumnsLoaded = false;
  protected $isIndexesLoaded = false;
  protected $database;

  function __construct($database, $name, $isExisting = false)
  {
    parent::__construct($name);
    $this->database = $database;
    $this->isExisting = $isExisting;
  }

  function getDatabase()
  {
    return $this->database;
  }

  //Based on code from Creole
  function loadColumns()
  {
    if($this->isExisting && !$this->isColumnsLoaded)
    {
      $connection = $this->database->getConnection();
      $queryId = $connection->execute("SHOW COLUMNS FROM `" . $this->name . "`");

      while($row = mysqli_fetch_assoc($queryId))
      {
        $name = $row['Field'];
        $isNullable =($row['Null'] == 'YES');
        $isAutoIncrement =(strpos($row['Extra'], 'auto_increment') !== false);
        $size = null;
        $precision = null;

        if(preg_match('/^(\w+)[\(]?([\d,]*)[\)]?( |$)/', $row['Type'], $matches))
        {
          //            colname[1]   size/precision[2]
          $nativeType = $matches[1];
          if($matches[2])
          {
            if(($cpos = strpos($matches[2], ',')) !== false)
            {
              $size = (int) substr($matches[2], 0, $cpos);
              $precision = (int) substr($matches[2], $cpos + 1);
            }
            else
            {
              $size = (int) $matches[2];
            }
          }
        }
        elseif(preg_match('/^(\w+)\(/', $row['Type'], $matches))
        {
          $nativeType = $matches[1];
        }
        else
        {
          $nativeType = $row['Type'];
        }

        // BLOBs can't have any default values in Mysqli
        $default = preg_match('~blob|text~', $nativeType) ?  null : $row['Default'];

        $this->columns[$name] = new lmbMysqliColumnInfo($this,
                    $name, $nativeType, $size, $precision, $isNullable, $default, $isAutoIncrement);
      }
      $this->isColumnsLoaded = true;
    }
  }

  //Based on code from loadColumns()
  function loadIndexes()
  {
    if(!$this->isExisting || $this->isIndexesLoaded)
      return;

    $connection = $this->database->getConnection();
    $queryId = $connection->execute("SHOW INDEX FROM `" . $this->name . "`");

    while($row = mysqli_fetch_assoc($queryId))
    {

      $index = new lmbMysqliIndexInfo();
      $index->column_name = $row['Column_name'];

      $index->name = $row['Key_name'];
      if ('PRIMARY' == $row['Key_name'])
      {
        $index->name = $index->column_name;
        $index->type = lmbDbIndexInfo::TYPE_PRIMARY;
      }
      else
      {
        $index->type = $row['Non_unique']
          ? lmbDbIndexInfo::TYPE_COMMON : lmbDbIndexInfo::TYPE_UNIQUE;
      }

      $this->indexes[$index->name] = $index;
    }
    $this->isIndexesLoaded = true;
  }
}


