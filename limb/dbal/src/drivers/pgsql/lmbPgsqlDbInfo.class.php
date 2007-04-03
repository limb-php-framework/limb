<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbPgsqlDbInfo.class.php 4994 2007-02-08 15:36:08Z pachanga $
 * @package    dbal
 */
lmb_require('limb/dbal/src/drivers/lmbDbInfo.class.php');
lmb_require('limb/dbal/src/drivers/pgsql/lmbPgsqlTableInfo.class.php');

class lmbPgsqlDbInfo extends lmbDbInfo
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

      $result = $this->connection->execute("SELECT oid, relname FROM pg_class
                                                WHERE relkind = 'r' AND relnamespace = (SELECT oid
                                                  FROM pg_namespace
                                                  WHERE
                                                       nspname NOT IN ('information_schema','pg_catalog')
                                                       AND nspname NOT LIKE 'pg_temp%'
                                                       AND nspname NOT LIKE 'pg_toast%'
                                                  LIMIT 1)
                                                ORDER BY relname");

      while($row = pg_fetch_assoc($result))
      {
        $this->tables[$row['relname']] = $row['oid'];
      }

      pg_free_result($result);
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
      $this->tables[$name] = new lmbPgsqlTableInfo($this, $name, true, $this->tables[$name]);
    }
    return $this->tables[$name];
  }
}

?>
