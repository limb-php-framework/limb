<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/cache/src/lmbCacheApcBackend.class.php');
lmb_require(dirname(__FILE__) . '/lmbCacheBackendTest.class.php');

class lmbCacheApcBackendTest extends lmbCacheBackendTest
{
  function skip()
  {
    $this->skipIf(!extension_loaded('apc'), 'APC extension not found. Test skipped.');
    $this->skipIf(!ini_get('apc.enabled'), 'APC extension not enabled. Test skipped.');
    $this->skipIf((!ini_get('apc.enable_cli') and php_sapi_name() == 'cli'), 'APC CLI not enabled. Test skipped.');
  }

  function _createPersisterImp()
  {
    return new lmbCacheApcBackend();
  }
  
  function testAddLock()
  {
    $this->assertTrue($this->cache->set(1, $v = 'value'));
    
    $this->assertFalse($this->cache->add(1, 'value_add'));
    $this->assertFalse($this->cache->add(1, 'value_add'), 'apc_add() deletes variables on second call, see http://pecl.php.net/bugs/bug.php?id=13735');
        
    $this->assertEqual($this->cache->get(1), $v, 'original value has been reseted by apc_add()');
    
    $this->assertTrue($this->cache->add(2, 'value2'));
    
    $this->cache->set(2, 'new value');
    $this->assertEqual($this->cache->get(2), 'new value');
  }

  function testGetWithTtlFalse()
  {
    $this->skipIf(true, 'APC caches time comparizon results within 1 request, so testGetWithTtlFalse cannot be run properly, see http://pecl.php.net/bugs/bug.php?id=13331.');
  }
}
