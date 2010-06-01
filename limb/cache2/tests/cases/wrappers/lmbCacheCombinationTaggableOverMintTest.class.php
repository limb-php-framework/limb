<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

lmb_require('limb/cache2/src/drivers/lmbCacheMemoryConnection.class.php');
lmb_require('limb/cache2/tests/cases/drivers/lmbCacheConnectionTest.class.php');

class lmbCacheCombinationTaggableOverMintTest extends lmbCacheConnectionTest
{
  function __construct()
  {
    $dir = lmb_var_dir() .'/cache2';
    $this->dsn = 'file:///'.$dir.'?wrapper[]=mint&wrapper[]=taggable';
  }

  function testAdd()
  {
    $key = $this->_getUniqueId('testAdd');

    $this->assertTrue($this->cache->add($key, 'foo', false, 'tag'));
    $this->assertFalse($this->cache->add($key, 'bar', false, 'tag'));
    $this->assertEqual($this->cache->get($key), 'foo');
  }
}
