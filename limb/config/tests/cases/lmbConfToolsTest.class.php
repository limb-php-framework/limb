<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/config/src/lmbConfTools.class.php');
lmb_require('limb/core/src/lmbSet.class.php');

class lmbConfToolsTest extends UnitTestCase
{
  function setUp()
  {
    lmbToolkit :: save();
  }

  function tearDown()
  {
    lmbToolkit :: restore();
  }
  
  function testSetHasGetConf()
  {
    $toolkit = lmbToolkit :: merge(new lmbConfTools());
    $conf_name = 'foo';
    $key = 'bar';
    $value = 42;
    
    $this->assertFalse($toolkit->hasConf($conf_name));        
    
    $toolkit->setConf($conf_name, array($key => $value));    
    $this->assertTrue($toolkit->hasConf($conf_name));
    
    $conf = $toolkit->getConf($conf_name);    
    $this->assertEqual($conf[$key], $value);
  }
}