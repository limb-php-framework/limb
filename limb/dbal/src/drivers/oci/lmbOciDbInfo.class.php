<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbOciDbInfo.class.php 4994 2007-02-08 15:36:08Z pachanga $
 * @package    dbal
 */
lmb_require('limb/dbal/src/drivers/lmbDbInfo.class.php');
lmb_require(dirname(__FILE__) . '/lmbOciTableInfo.class.php');

class lmbOciDbInfo extends lmbDbInfo
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
      $config = $this->connection->getConfig();
      $schema = strtoupper($config['user']);
      $result = $this->connection->execute("SELECT TABLE_NAME FROM ALL_TABLES WHERE OWNER = '$schema'");

      while($row = oci_fetch_assoc($result))
      {
        $this->tables[strtolower($row['TABLE_NAME'])] = 1;
      }

      oci_free_statement($result);
      $this->isTablesLoaded = true;
    }
  }

  function getTable($name)
  {
    if(!$this->hasTable($name))
    {
      throw new lmbDbException('Table does not exist ' . $name);
    }
    if(!is_object($this->tables[$name]))
    {
      $config = $this->connection->getConfig();
      $this->tables[$name] = new lmbOciTableInfo($this, $name, $config['user'], true);
    }
    return $this->tables[$name];
  }
}

?>
