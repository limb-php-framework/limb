<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/cache/src/lmbCacheMemcacheBackend.class.php');
lmb_require(dirname(__FILE__) . '/lmbCacheBackendTest.class.php');

class lmbCacheMemcacheBackendTest extends lmbCacheBackendTest
{
  function skip()
  {
    $this->skipIf(!extension_loaded('memcache'), 'Memcache extension not found. Test skipped.');
    $this->skipIf(!class_exists('Memcache'), 'Memcache class not found. Test skipped.');
  }
  
  function _createPersisterImp()
  {
    return new lmbCacheMemcacheBackend();
  }
  
}
