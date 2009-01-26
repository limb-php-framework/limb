<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/cache/src/lmbCacheMemoryBackend.class.php');
lmb_require(dirname(__FILE__) . '/lmbCacheBackendTest.class.php');

class lmbCacheMemoryBackendTest extends lmbCacheBackendTest
{
  function _createPersisterImp()
  {
    return new lmbCacheMemoryBackend();
  }
  
  function  testGetWithTtlFalse()
  {
    return;
  }
  
  function testObjectClone()
  {
    return;
  }
  
}
