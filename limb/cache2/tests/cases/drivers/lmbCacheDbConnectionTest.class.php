<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

lmb_require('limb/cache2/src/drivers/lmbCacheDbConnection.class.php');
lmb_require(dirname(__FILE__) . '/lmbCacheConnectionTest.class.php');

lmb_require('limb/dbal/src/lmbDbDump.class.php');

class lmbCacheDbConnectionTest extends lmbCacheConnectionTest
{
  protected $storage_init_file = 'limb/dbal/common.inc.php';

  function _getDumpFile()
  {
    $type = lmbToolkit :: instance()->getDefaultDbConnection()->getType();

    $dump_file = dirname(__FILE__) . '/../../../init/cache.' . $type;
    return file_exists($dump_file) ? $dump_file : null;
  }

  function skip()
  {
    $is_connection_exists = lmbToolkit :: instance()->isDefaultDbDSNAvailable();
    $this->skipIf(!$is_connection_exists, 'DB connection not found. Test skiped');
    if($is_connection_exists)
      $this->skipIf(!$this->_getDumpFile(), 'Dump file for type "'.lmbToolkit :: instance()->getDefaultDbConnection()->getType().'" not found. Test skiped');
  }

  function setUp()
  {
    $this->dump = new lmbDbDump($this->_getDumpFile());
    $this->dump->load();

    parent::setUp();
  }

  function tearDown()
  {
    parent::tearDown();

    $this->dump->clean();
  }

  function __construct()
  {
    $this->dsn = 'db://dsn?table=lmb_cache2';
    parent::__construct();
  }
}
