<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/dbal/src/dump/lmbSQLDumpLoader.class.php');

/**
 * class lmbDbDump.
 *
 * @package dbal
 * @version $Id: lmbDbDump.class.php 6221 2007-08-07 07:24:35Z pachanga $
 */
class lmbDbDump
{
  protected $file;
  protected $loader;
  protected $connection;

  function __construct($file, $connection = null)
  {
    $this->file = $file;

    if($connection)
      $this->connection = $connection;
    else
      $this->connection = lmbToolkit :: instance()->getDefaultDbConnection();
  }

  function load()
  {
    $type = $this->connection->getType();

    $default_loader = 'lmbSQLDumpLoader';
    $loader = 'lmb' . ucfirst($type) . 'DumpLoader';

    if(file_exists(dirname(__FILE__) . '/dump/' . $loader . '.class.php'))
      require_once(dirname(__FILE__) . '/dump/' . $loader . '.class.php');
    else
      $loader = $default_loader;

    $this->loader = new $loader($this->file);
    $this->loader->execute($this->connection);
  }

  function clean()
  {
    $this->loader->cleanTables($this->connection);
  }
}


