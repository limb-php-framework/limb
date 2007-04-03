<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbPrefixedFormCommandTest.class.php 5012 2007-02-08 15:38:06Z pachanga $
 * @package    web_app
 */
lmb_require('limb/web_app/src/command/lmbPrefixedFormCommand.class.php');

class lmbPrefixedFormCommandTest extends UnitTestCase
{
  var $request;
  var $toolkit;

  function setUp()
  {
    $this->toolkit = lmbToolkit :: save();
    $this->request = $this->toolkit->getRequest();
  }

  function tearDown()
  {
    lmbToolkit :: restore();
  }

  function testNotSubmittedSinceUsingPrefix()
  {
    $this->toolkit->setRequest($request = new lmbHttpRequest(null, $get = null, $post = array('submitted' => 1)));

    $command = new lmbPrefixedFormCommand('some_template', 'form_id');

    $command->perform();

    $this->assertFalse($command->isSubmitted());
  }

  function testSubmitted()
  {
    $form_data = array('name' => 'Some name');

    $this->toolkit->setRequest($request = new lmbHttpRequest(null, $get = null, $post = array('special_form' => $form_data)));

    $command = new lmbPrefixedFormCommand('some_template', $form_name = 'special_form');

    $command->perform();

    $this->assertTrue($command->isSubmitted());
  }

  function testSubmittedIfJustArray()
  {
    $form_data = array();

    $this->toolkit->setRequest($request = new lmbHttpRequest(null, $get = null, $post = array('special_form' => $form_data)));

    $command = new lmbPrefixedFormCommand('some_template', $form_name = 'special_form');

    $command->perform();

    $this->assertTrue($command->isSubmitted());
  }
}

?>