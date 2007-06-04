<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbErrorListTest.class.php 5933 2007-06-04 13:06:23Z pachanga $
 * @package    $package$
 */
lmb_require('limb/validation/src/lmbErrorList.class.php');

class lmbErrorListTest extends UnitTestCase
{
  function testAddFieldError()
  {
    $list = new lmbErrorList();

    $this->assertTrue($list->isValid());

    $list->addError($message = 'error_group', array('foo'), array('FOO'));

    $this->assertFalse($list->isValid());

    $errors = $list->export();
    $this->assertEqual(sizeof($errors), 1);
    $this->assertEqual($errors[0]->getMessage(), $message);
    $this->assertEqual($errors[0]->getFields(), array('foo'));
    $this->assertEqual($errors[0]->getValues(), array('FOO'));
  }
}
?>