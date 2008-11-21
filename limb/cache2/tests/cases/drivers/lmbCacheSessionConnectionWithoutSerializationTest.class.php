<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

lmb_require('limb/cache2/src/drivers/lmbCacheSessionConnection.class.php');
lmb_require(dirname(__FILE__) . '/lmbCacheSessionConnectionTest.class.php');

class lmbCacheSessionConnectionWithoutSerializationTest extends lmbCacheSessionConnectionTest
{ 
  function __construct()
  { 
    parent::__construct();   
    $this->dsn = 'session:?need_serialization=0';    
  } 
  
  function testObjectClone()
  {
    // can't work without serilization
  }
}
