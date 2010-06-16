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

  private $_host = 'localhost';

  private $_port = 11211;

  function skip()
  {
    $this->skipIf(!extension_loaded('memcache'), 'Memcache extension not found. Test skipped.');
    $this->skipIf(!class_exists('Memcache'), 'Memcache class not found. Test skipped.');
    if (class_exists('Memcache'))
    {
      $memcache = new Memcache();
      $this->skipIf(!@$memcache->connect($this->_host, $this->_port), "memcached is not running on $this->_host:$this->_port. Test skipped.");
      @$memcache->close();
    }
  }
  
  function _createPersisterImp()
  {
    return new lmbCacheMemcacheBackend();
  }
  
}
