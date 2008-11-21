<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/cache2/src/drivers/lmbCacheApcConnection.class.php');
lmb_require(dirname(__FILE__) . '/lmbCacheConnectionTest.class.php');

class lmbCacheApcConnectionTest extends lmbCacheConnectionTest
{
  function __construct()
  {
    $this->dsn = 'apc:';
  }

  function skip()
  {
    $this->skipIf(!extension_loaded('apc'), 'APC extension not found. Test skipped.');
  }  
  
  function testGetWithTtl_sameThread()
  {
    // APC cache all variables in one thread 
  }
  
  function testGetWithTtl_differentThread()
  {
    // APC works strange in cli mode
  }
}
