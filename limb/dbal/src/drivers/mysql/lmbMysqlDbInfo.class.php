<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbMysqlDbInfo.class.php 4994 2007-02-08 15:36:08Z pachanga $
 * @package    dbal
 */
lmb_require('limb/dbal/src/drivers/lmbDbInfo.class.php');
lmb_require('limb/dbal/src/drivers/mysql/lmbMysqlTableInfo.class.php');

class lmbMysqlDbInfo extends lmbDbInfo
{
  protected $connection;
  protected $isExisting = false;
  protected $isTablesLoaded = false;

  function __construct($connection, $name, $isExisting = false)
  {
    $this->connection = $connection;
    $this->isExisting = $isExisting;
    parent::__construct($name);
  }

  function getConnection()
  {
    return $this->connection;
  }

  function loadTables()
  {
    if($this->isExisting && !$this->isTablesLoaded)
    {
      $queryId = $this->connection->execute("SHOW TABLES FROM `" . $this->name . "`");
      while(is_array($row = mysql_fetch_row($queryId)))
      {
        $this->tables[$row[0]] = null;
      }
      mysql_free_result($queryId);
      $this->isTablesLoaded = true;
    }
  }

  function getTable($name)
  {
    if(!$this->hasTable($name))
    {
      throw new lmbDbException("Table does not exist '$name'");
    }
    if(is_null($this->tables[$name]))
    {
      $this->tables[$name] = new lmbMysqlTableInfo($this, $name, true);
    }
    return $this->tables[$name];
  }
}

?>
