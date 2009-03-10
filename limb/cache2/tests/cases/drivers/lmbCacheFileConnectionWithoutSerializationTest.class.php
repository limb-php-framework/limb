<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

lmb_require('limb/cache2/src/drivers/lmbCacheMemoryConnection.class.php');
lmb_require(dirname(__FILE__) . '/lmbCacheFileConnectionTest.class.php');

class lmbCacheFileConnectionWithoutSerializationTest extends lmbCacheFileConnectionTest
{
  function __construct()
  {
    $dir = LIMB_VAR_DIR . '/cache';
    $this->dsn = 'file:///' . $dir . '?need_serialization=0';
  }
  
  function testObjectClone()
  {
    // can't work without serilization
  }
  
  function testGet_Positive_FalseValue()
  {
    // can't work without serilization
  }
  
  function testProperSerializing()
  {
    // can't work without serilization
  }
  
  function _getCachedValues()
  {
    return array(
      'some value',
   );
  }
}
