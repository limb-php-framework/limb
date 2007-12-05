<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/core/src/lmbObject.class.php');
lmb_require('limb/cache/src/lmbCacheApcBackend.class.php');
lmb_require(dirname(__FILE__) . '/lmbCacheBackendTest.class.php');

class lmbCacheApcBackendTest extends lmbCacheBackendTest
{
  function skip()
  {
    $this->skipIf(!extension_loaded('apc'), 'APC extension not found. Test skipped.');
    $this->skipIf(!ini_get('apc.enabled'), 'APC extension not enabled. Test skipped.');
  }

  function _createPersisterImp()
  {
    return new lmbCacheApcBackend();
  }
  
}
