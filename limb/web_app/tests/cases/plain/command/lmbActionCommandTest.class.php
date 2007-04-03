<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbActionCommandTest.class.php 5012 2007-02-08 15:38:06Z pachanga $
 * @package    web_app
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