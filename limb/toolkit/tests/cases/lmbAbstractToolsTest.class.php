<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbAbstractToolsTest.class.php 5933 2007-06-04 13:06:23Z pachanga $
 * @package    $package$
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

?>
