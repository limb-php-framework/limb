<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbMysqlTableInfo.class.php 4994 2007-02-08 15:36:08Z pachanga $
 * @package    dbal
 */
lmb_require('limb/dbal/src/drivers/lmbDbTableInfo.class.php');
lmb_require('limb/dbal/src/drivers/mysql/lmbMysqlColumnInfo.class.php');

class lmbMysqlTableInfo extends lmbDbTableInfo
{
  protected $isExisting = false;
  protected $isColumnsLoaded = false;
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

      while($row = mysql_fetch_assoc($queryId))
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

        // BLOBs can't have any default values in MySQL
        $default = preg_match('~blob|text~', $nativeType) ?  null : $row['Default'];

        $this->columns[$name] = new lmbMysqlColumnInfo($this,
                    $name, $nativeType, $size, $precision, $isNullable, $default, $isAutoIncrement);
      }
      $this->isColumnsLoaded = true;
    }
  }
}

?>
