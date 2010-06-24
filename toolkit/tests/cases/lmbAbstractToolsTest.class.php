<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/toolkit/src/lmbAbstractTools.class.php');

class TestAbstractTools extends lmbAbstractTools
{
  function foo(){}
  function bar(){}
}

class lmbAbstractToolsTest extends UnitTestCase
{
  function testGetToolsSignatures()
  {
    $tools = new TestAbstractTools();
    $this->assertEqual($tools->getToolsSignatures(),
                       array('foo' => $tools, 'bar' => $tools));
  }
}


