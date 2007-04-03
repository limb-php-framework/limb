<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbStaticToolsTest.class.php 5007 2007-02-08 15:37:18Z pachanga $
 * @package    toolkit
 */
lmb_require('limb/toolkit/src/lmbStaticTools.class.php');

class lmbStaticToolsTest extends UnitTestCase
{
  function setUp()
  {
    lmbToolkit :: save();
  }

  function tearDown()
  {
    lmbToolkit :: restore();
  }

  function testGetToolsSignatures()
  {
    $tools = new lmbStaticTools(array('foo' => 'a', 'bar' => 'b'));

    $toolkit = lmbToolkit :: setup($tools);

    $this->assertEqual($toolkit->foo(), 'a');
    $this->assertEqual($toolkit->bar(), 'b');
  }
}

?>
