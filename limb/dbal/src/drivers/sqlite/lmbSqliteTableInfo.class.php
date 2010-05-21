<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/dbal/src/drivers/lmbDbTableInfo.class.php');
lmb_require('limb/dbal/src/drivers/sqlite/lmbSqliteColumnInfo.class.php');

/**
 * class lmbSqliteTableInfo.
 *
 * @package dbal
 * @version $Id$
 */
class lmbSqliteTableInfo extends lmbDbTableInfo
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
      $sql = "PRAGMA table_info('" . $this->name . "')";
      $queryId = $connection->execute($sql);

      while($row = sqlite_fetch_array($queryId, SQLITE_ASSOC))
      {
        $name = $row['name'];

        $fulltype = $row['type'];
        $size = null;
        $scale = null;
        if(preg_match('/^([^\(]+)\(\s*(\d+)\s*,\s*(\d+)\s*\)$/', $fulltype, $matches))
        {
          $type = $matches[1];
          $size = $matches[2];
          $scale = $matches[3]; // aka precision
        }
        elseif(preg_match('/^([^\(]+)\(\s*(\d+)\s*\)$/', $fulltype, $matches))
        {
          $type = $matches[1];
          $size = $matches[2];
        }
        else
          $type = $fulltype;

        // If column is primary key and of type INTEGER, it is auto increment
        // See: http://sqlite.org/faq.html#q1
        $is_auto_increment = ($row['pk'] == 1 && $fulltype == 'INTEGER');
        $not_null = $row['notnull'];
        $is_nullable = !$not_null;

        $default_val = $row['dflt_value'];

        $this->columns[$name] = new lmbSqliteColumnInfo($this, $name, $type, $size, $scale,
                                                        $is_nullable, $default_val, $is_auto_increment);

        if(($row['pk'] == 1) || (strtolower($type) == 'integer primary key'))
        {
          //primary key handling...
        }
      }
      $this->isColumnsLoaded = true;
    }
  }

  function loadIndexes()
  {
    lmb_require('limb/core/src/exception/lmbNotYetImplementedException.class.php');
    throw new lmbNotYetImplementedException();
  }
}


