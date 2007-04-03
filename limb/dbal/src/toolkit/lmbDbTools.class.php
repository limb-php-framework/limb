<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbDbTools.class.php 5427 2007-03-29 14:54:01Z pachanga $
 * @package    dbal
 */
lmb_require('limb/toolkit/src/lmbAbstractTools.class.php');
lmb_require('limb/dbal/src/lmbDBAL.class.php');
lmb_require('limb/dbal/src/lmbDbDSN.class.php');

class lmbDbTools extends lmbAbstractTools
{
  protected $default_connection;
  protected $default_db_config;
  protected $db_tables = array();

  function setDefaultDbDSN($conf)
  {
    $this->default_db_config = new lmbDbDSN($conf);
  }

  function getDefaultDbDSN()
  {
    if(is_object($this->default_db_config))
      return $this->default_db_config;

    $conf = $this->toolkit->getConf('db');
    $this->default_db_config = new lmbDbDSN($conf->get('dsn'));

    return $this->default_db_config;
  }

  function getDefaultDbConnection()
  {
    if(is_object($this->default_connection))
      return $this->default_connection;

    if(!is_object($dsn = $this->toolkit->getDefaultDbDSN()))
      throw new lmbException('Default database DSN is not valid');

    $this->default_connection = lmbDBAL :: newConnection($dsn);
    return $this->default_connection;
  }

  function setDefaultDbConnection($conn)
  {
    $this->default_connection = $conn;
  }

  function createTableGateway($table_name)
  {
    if(isset($this->db_tables[$table_name]))
      return $this->db_tables[$table_name];

    $db_table = new lmbTableGateway($table_name);
    $this->db_tables[$table_name] = $db_table;
    return $db_table;
  }
}
?>
