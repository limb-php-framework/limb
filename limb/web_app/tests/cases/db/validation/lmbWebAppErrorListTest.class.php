<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbWebAppErrorListTest.class.php 5528 2007-04-04 15:08:50Z pachanga $
 * @package    web_app
 */
lmb_require('limb/web_app/src/validation/lmbWebAppErrorList.class.php');

class lmbWebAppErrorListTest extends UnitTestCase
{
  var $message_box;

  function setUp()
  {
    $this->message_box = lmbToolkit :: save()->getMessageBox();
  }

  function tearDown()
  {
    lmbToolkit :: restore();
  }

  function testAddError()
  {
    $list = new lmbWebAppErrorList();

    $this->assertTrue($list->isValid());

    $list->addError($message = 'error_group', array('foo'), array('FOO'));

    $this->assertFalse($list->isValid());

    $this->assertEqual($list->count(), 1);

    $errors = $list->export();
    $this->assertEqual(sizeof($errors), 1);
    $this->assertEqual($errors[0]->getMessage(), $message);
    $this->assertEqual($errors[0]->getFields(), array('foo'));
    $this->assertEqual($errors[0]->getValues(), array('FOO'));

    $this->assertTrue($this->message_box->hasErrors());
  }
}
?>