<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
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