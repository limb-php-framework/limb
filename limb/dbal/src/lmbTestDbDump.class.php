<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/dbal/src/dump/lmbSQLDumpLoader.class.php');

class lmbTestDbDump
{
  protected $loader;
  protected $connection;

  function __construct($file = null, $connection = null)
  {
    if($connection)
      $this->connection = $connection;
    else
      $this->connection = lmbToolkit :: instance()->getDefaultDbConnection();

    if(!is_null($file))
      $this->_load($file);
  }

  function _load($file)
  {
    $type = $this->connection->getType();

    $default_loader = 'lmbSQLDumpLoader';
    $loader = 'lmb' . ucfirst($type) . 'DumpLoader';

    if(file_exists(dirname(__FILE__) . '/dump/' . $loader . '.class.php'))
      require_once(dirname(__FILE__) . '/dump/' . $loader . '.class.php');
    else
      $loader = $default_loader;

    $this->loader = new $loader($file);
    $this->loader->execute($this->connection);
  }

  function clean()
  {
    $this->loader->cleanTables($this->connection);
  }
}

?>
