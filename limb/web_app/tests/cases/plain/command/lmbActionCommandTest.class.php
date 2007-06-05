<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/web_app/src/command/lmbActionCommand.class.php');

class lmbActionCommandTest extends UnitTestCase
{
  var $toolkit;

  function setUp()
  {
    $this->toolkit = lmbToolkit :: save();
  }

  function tearDown()
  {
    lmbToolkit :: restore();
  }

  function testChangesViewTemplatePath()
  {
    $command = new lmbActionCommand($path = 'some_template');
    $command->perform();

    $this->assertEqual($this->toolkit->getView()->getTemplate(), $path);
  }
}

?>