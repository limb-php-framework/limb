<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

lmb_require('limb/cache2/src/drivers/lmbCacheMemoryConnection.class.php');
lmb_require(dirname(__FILE__) . '/lmbCacheConnectionTest.class.php');

require_once('limb/dbal/src/lmbDbDump.class.php');

lmb_require('limb/dbal/common.inc.php');

class lmbCacheDbConnectionTest extends lmbCacheConnectionTest
{  
  function setUp()
  {
    $type = lmbToolkit :: instance()->getDefaultDbConnection()->getType();    
    $this->dump = new lmbDbDump(dirname(__FILE__) . '/../../../init/cache2.' . $type);
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
  }
}
