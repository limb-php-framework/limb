<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/dbal/src/drivers/lmbDbInfo.class.php');
lmb_require('limb/dbal/src/drivers/pgsql/lmbPgsqlTableInfo.class.php');

/**
 * class lmbPgsqlDbInfo.
 *
 * @package dbal
 * @version $Id$
 */
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
    if($this->isExisting)
    {
      $schema = $this->connection->getConfig()->get('schema', null);
      if (!is_null($schema))
        $schema_chain = array($schema);
      else
        $schema_chain = array('public', $this->connection->getConfig()->getUser());

      $result = $this->connection->execute("
        SELECT oid, relname
          FROM pg_class
          WHERE relkind = 'r' AND relnamespace =
          (
            SELECT oid
              FROM pg_namespace
              WHERE
                nspname NOT IN ('information_schema','pg_catalog') AND
                nspname NOT LIKE 'pg_temp%' AND
                nspname NOT LIKE 'pg_toast%' AND
                nspname IN ('" . implode("', '", $schema_chain) . "')
              ORDER BY oid DESC
              LIMIT 1
          )
          ORDER BY relname
      ");

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


