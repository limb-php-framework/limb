<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/dbal/src/dump/lmbSQLDumpLoader.class.php');

/**
 * class lmbDbDump.
 *
 * @package dbal
 * @version $Id: lmbDbDump.class.php 8070 2010-01-20 08:19:23Z korchasa $
 */
class lmbDbDump
{
  protected $file;
  protected $loader;
  /**
   * @var lmbDbConnection
   */
  protected $connection;

  function __construct($file = null, $connection = null)
  {
    $this->file = $file;

    if($connection)
      $this->connection = $connection;
    else
      $this->connection = lmbToolkit :: instance()->getDefaultDbConnection();
  }

  function load($file = null)
  {
    $type = $this->connection->getType();

    $default_loader = 'lmbSQLDumpLoader';
    $loader = 'lmb' . ucfirst($type) . 'DumpLoader';

    if(file_exists(dirname(__FILE__) . '/dump/' . $loader . '.class.php'))
      require_once(dirname(__FILE__) . '/dump/' . $loader . '.class.php');
    else
      $loader = $default_loader;

    $file = ($file) ? $file : $this->file;
    $this->loader = new $loader($file);
    $this->loader->execute($this->connection);

    $this->connection->getDatabaseInfo()->loadTables();
  }

  function clean()
  {
    $this->loader->cleanTables($this->connection);
  }
}


